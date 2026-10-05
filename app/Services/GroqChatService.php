<?php

namespace App\Services;

use App\Models\ChatbotMessage;
use App\Models\Client;
use App\Models\FinancialReceipt;
use App\Models\Hearing;
use App\Models\LegalCase;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqChatService
{
    protected string $apiKey;
    protected string $defaultModel;
    protected array $models;
    protected float $temperature;
    protected int $maxTokens;

    public function __construct()
    {
        $this->apiKey       = config('groq.api_key', env('GROQ_API_KEY', ''));
        $this->defaultModel = config('groq.model', env('GROQ_MODEL', 'llama-3.3-70b-versatile'));
        $this->models       = config('groq.models', [
            'llama-3.3-70b-versatile',
            'llama-3.1-8b-instant',
            'allam-2-7b',
            'qwen/qwen3.6-27b',
            'openai/gpt-oss-20b',
        ]);
        $this->temperature  = (float) config('groq.temperature', 0.7);
        $this->maxTokens    = (int) config('groq.max_tokens', 1500);
    }

    /**
     * Handle chat request with Groq AI with automatic Model Fallback Chain.
     */
    public function ask(string $prompt, array $history = [], ?string $locale = null, ?int $userId = null, ?int $officeId = null, ?string $sessionId = null): array
    {
        $locale = $locale ?? app()->getLocale();

        // 1. Build Mizan Office Knowledge Context
        $officeContext = $this->buildLawOfficeContext($officeId, $locale);

        // 2. Prepare System Instruction
        $systemPrompt = $locale === 'ar'
            ? config('groq.system_prompt_ar')
            : config('groq.system_prompt_en');

        $fullSystemContext = $systemPrompt . "\n\n" . $officeContext;

        // 3. Fallback if API key is missing
        if (empty($this->apiKey)) {
            $result = $this->generateFallbackResponse($prompt, $locale, $officeId);
            $this->saveChatMessage($prompt, $result['reply'], $result['referenced_cases'] ?? [], $userId, $officeId, $sessionId);
            return $result;
        }

        // 4. Build OpenAI-compatible Messages Payload for Groq
        $messages = [];
        $messages[] = [
            'role'    => 'system',
            'content' => $fullSystemContext,
        ];

        foreach ($history as $msg) {
            $role    = ($msg['role'] ?? 'user') === 'model' || ($msg['role'] ?? 'user') === 'assistant' ? 'assistant' : 'user';
            $content = $msg['content'] ?? $msg['text'] ?? '';
            if (! empty($content)) {
                $messages[] = [
                    'role'    => $role,
                    'content' => $content,
                ];
            }
        }

        $messages[] = [
            'role'    => 'user',
            'content' => $prompt,
        ];

        // 5. Automatic Model Fallback Chain Processing
        $modelsToTry = array_unique(array_merge([$this->defaultModel], $this->models));
        $endpoint    = 'https://api.groq.com/openai/v1/chat/completions';

        foreach ($modelsToTry as $currentModel) {
            try {
                $payload = [
                    'model'       => $currentModel,
                    'messages'    => $messages,
                    'temperature' => $this->temperature,
                    'max_tokens'  => $this->maxTokens,
                ];

                $response = Http::timeout(8)
                    ->withHeaders([
                        'Authorization' => "Bearer {$this->apiKey}",
                        'Content-Type'  => 'application/json',
                    ])
                    ->post($endpoint, $payload);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $replyText    = $responseData['choices'][0]['message']['content'] ?? null;

                    if ($replyText) {
                        // Clean up reasoning tags & artifacts
                        $cleanReplyText = preg_replace('/<think>[\s\S]*?<\/think>/i', '', $replyText);
                        $cleanReplyText = preg_replace('/\[(SYSTEM|ID:\s*\d+|ID)\][^\n]*\n?/i', '', $cleanReplyText);
                        $cleanReplyText = preg_replace('/^(أعتذر|عذراً|أسف|عذرا|I apologize|Sorry)[\s\S]*?(؟|\.|\!\n|\n)/u', '', $cleanReplyText);
                        $cleanReplyText = preg_replace('/(بصفتي مساعد ذكاء اصطناعي،? ليس لديّ? اسمٌ? حقيقي[\.\،\!]?|كمساعد ذكاء (اصطناعي|صناعي)[^،\.\!\n]*[،\.\!\n]?)/u', 'أنا مستشار الميزان الذكي ⚖️، ', $cleanReplyText);
                        $cleanReplyText = trim($cleanReplyText);

                        if (! empty($cleanReplyText)) {
                            $referencedCases = $this->extractReferencedCases($cleanReplyText, $officeId);

                            $this->saveChatMessage($prompt, $cleanReplyText, $referencedCases, $userId, $officeId, $sessionId);

                            Log::info("Groq Chat successfully responded using model [{$currentModel}]");

                            return [
                                'status'           => true,
                                'model'            => $currentModel,
                                'reply'            => $cleanReplyText,
                                'referenced_cases' => $referencedCases,
                            ];
                        }
                    }
                }

                Log::warning("Groq model [{$currentModel}] returned HTTP {$response->status()}. Failing over to next model...");
            } catch (\Exception $e) {
                Log::warning("Groq model [{$currentModel}] exception: {$e->getMessage()}. Failing over...");
            }
        }

        // 6. Hard Fallback Response if all Groq models fail or hit rate limits
        Log::error("All Groq AI models failed or hit rate limits. Serving fallback response.");
        $result = $this->generateFallbackResponse($prompt, $locale, $officeId);
        $this->saveChatMessage($prompt, $result['reply'], $result['referenced_cases'] ?? [], $userId, $officeId, $sessionId);
        return $result;
    }

    /**
     * Save message to chatbot_messages table.
     */
    protected function saveChatMessage(string $prompt, string $reply, array $referencedCases, ?int $userId = null, ?int $officeId = null, ?string $sessionId = null): void
    {
        try {
            ChatbotMessage::create([
                'user_id'          => $userId,
                'office_id'        => $officeId,
                'session_id'       => $sessionId,
                'prompt'           => $prompt,
                'reply'            => $reply,
                'referenced_cases' => $referencedCases,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save chatbot message: ' . $e->getMessage());
        }
    }

    /**
     * Build live law office metrics context for system prompt.
     */
    protected function buildLawOfficeContext(?int $officeId, string $locale): string
    {
        if (! $officeId) {
            return "ملاحظة: المستخدم حالياً غير مرتبط بمكتب محاماة معين.\n";
        }

        return Cache::remember("groq_office_context_{$officeId}_{$locale}", 15, function () use ($officeId, $locale) {
            $totalCases      = LegalCase::where('office_id', $officeId)->count();
            $activeCases     = LegalCase::where('office_id', $officeId)->where('status', 'مستمرة')->count();
            $closedCases     = LegalCase::where('office_id', $officeId)->where('status', 'منتهية')->count();

            $totalFees       = LegalCase::where('office_id', $officeId)->sum('total_fees');
            $totalPaidFees   = LegalCase::where('office_id', $officeId)->sum('paid_fees');
            $remainingFees   = LegalCase::where('office_id', $officeId)->sum('remaining_fees');

            $totalClients    = Client::where('office_id', $officeId)->count();

            // Today's Hearings
            $todayHearings = Hearing::with(['legalCase', 'court', 'hearingType'])
                ->where('office_id', $officeId)
                ->whereDate('hearing_date', today())
                ->get();

            $todayHearingsSummary = "";
            if ($todayHearings->count() > 0) {
                $todayHearingsSummary .= "جلسات المحكمة لليوم (" . today()->format('Y-m-d') . "):\n";
                foreach ($todayHearings as $h) {
                    $caseNum   = $h->legalCase?->case_number ?? 'غير مسمى';
                    $courtName = $h->court?->name ?? 'المحكمة';
                    $time      = $h->hearing_time ? " الساعة {$h->hearing_time}" : '';
                    $type      = $h->hearingType?->name ?? 'جلسة';
                    $todayHearingsSummary .= "- قضية رقم: {$caseNum} | المحكمة: {$courtName} | نوع الجلسة: {$type}{$time}\n";
                }
            } else {
                $todayHearingsSummary .= "لا توجد جلسات محكمة مجدولة لليوم.\n";
            }

            // Upcoming Hearings (Next 7 days)
            $upcomingHearingsCount = Hearing::where('office_id', $officeId)
                ->whereBetween('hearing_date', [today()->addDay(), today()->addDays(7)])
                ->count();

            // Recent Legal Cases (Top 5)
            $recentCases = LegalCase::where('office_id', $officeId)->latest('id')->take(5)->get();
            $recentCasesSummary = "أبرز القضايا المسجلة مؤخراً بالمكتب:\n";
            foreach ($recentCases as $c) {
                $recentCasesSummary .= "- قضية #{$c->case_number} | نوع القضية: {$c->case_type} | الصفة: {$c->client_role} | الخصم: {$c->opponent_name} | الأتعاب الكلية: {$c->total_fees} EGP (المتبقي: {$c->remaining_fees} EGP)\n";
            }

            // Service requests
            $myServiceRequestsCount = ServiceRequest::where('requester_office_id', $officeId)->count();
            $assignedServicesCount  = ServiceRequest::where('assigned_office_id', $officeId)->count();

            if ($locale === 'ar') {
                return "=== بيانات وإحصائيات مكتب المحاماة المباشرة (LIVE LAW OFFICE DATA) ===\n" .
                       "- إجمالي القضايا بالمكتب: {$totalCases} قضية (القضايا المستمرة النشطة: {$activeCases}، المنتهية: {$closedCases})\n" .
                       "- المبالغ والأتعاب الكلية: " . number_format($totalFees, 2) . " EGP\n" .
                       "- إجمالي المقبوضات/المحصول: " . number_format($totalPaidFees, 2) . " EGP\n" .
                       "- إجمالي المستحقات المتبقية لدى العملاء: " . number_format($remainingFees, 2) . " EGP\n" .
                       "- إجمالي عدد موكلي/عملاء المكتب: {$totalClients} موكل\n" .
                       "- عدد الجلسات خلال الـ 7 أيام القادمة: {$upcomingHearingsCount} جلسة\n" .
                       "- عدد طلبات الإنابة الصادرة من المكتب: {$myServiceRequestsCount} | التكليفات المسندة للمكتب: {$assignedServicesCount}\n\n" .
                       $todayHearingsSummary . "\n" .
                       $recentCasesSummary;
            } else {
                return "=== LIVE LAW OFFICE DATA ===\n" .
                       "- Total Cases: {$totalCases} (Active: {$activeCases}, Closed: {$closedCases})\n" .
                       "- Total Legal Fees: " . number_format($totalFees, 2) . " EGP\n" .
                       "- Total Collected Fees: " . number_format($totalPaidFees, 2) . " EGP\n" .
                       "- Remaining Unpaid Fees: " . number_format($remainingFees, 2) . " EGP\n" .
                       "- Total Office Clients: {$totalClients}\n" .
                       "- Upcoming Hearings (Next 7 Days): {$upcomingHearingsCount}\n\n" .
                       $todayHearingsSummary . "\n" .
                       $recentCasesSummary;
            }
        });
    }

    /**
     * Match cases mentioned in prompt or reply.
     */
    protected function extractReferencedCases(string $replyText, ?int $officeId): array
    {
        if (! $officeId) return [];

        $cases = LegalCase::where('office_id', $officeId)->get();
        $matched = [];

        foreach ($cases as $c) {
            if ($c->case_number && mb_strpos($replyText, $c->case_number) !== false) {
                $matched[] = [
                    'id'          => $c->id,
                    'case_number' => $c->case_number,
                    'case_type'   => $c->case_type,
                    'status'      => $c->status,
                ];
            }
        }

        return $matched;
    }

    /**
     * Fallback response if Groq API key is missing or fails.
     */
    protected function generateFallbackResponse(string $prompt, string $locale, ?int $officeId): array
    {
        $keywords = mb_strtolower($prompt);

        if (mb_strpos($keywords, 'جلس') !== false || mb_strpos($keywords, 'hearing') !== false) {
            $reply = "أهلاً بك! يمكنك الاطلاع على أجندة الجلسات اليومية والقادمة مباشرة عبر قسم الجلسات في نظام الميزان. هل تود استعراض جلسة قضية معينة؟";
        } elseif (mb_strpos($keywords, 'أتعاب') !== false || mb_strpos($keywords, 'مال') !== false || mb_strpos($keywords, 'متبق') !== false) {
            $reply = "يوفر لك نظام الميزان إحصائيات مالية كاملة عن مقبوضات المكتب والأتعاب المتبقية لدى الموكلين مع إمكانية إصدار سندات قبض فورية.";
        } else {
            $reply = "مرحباً بك في مستشار الميزان الذكي ⚖️! أنا في خدمتك لمساعدتك في إدارة ملفات قضاياك، متابعة الجلسات، الأتعاب وصياغة العرائض القانونية. كيف يمكنني مساعدتك اليوم؟";
        }

        return [
            'status'           => true,
            'reply'            => $reply,
            'referenced_cases' => [],
        ];
    }
}
