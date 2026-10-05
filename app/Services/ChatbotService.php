<?php

namespace App\Services;

use App\Models\ChatbotMessage;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    public function __construct(protected GroqChatService $groqChatService) {}

    /**
     * Handle user chat prompt with Groq AI.
     */
    public function chat(?int $userId, ?int $officeId, array $data): array
    {
        try {
            $prompt    = $data['prompt'];
            $history   = $data['history'] ?? [];
            $sessionId = $data['session_id'] ?? null;

            $result = $this->groqChatService->ask($prompt, $history, null, $userId, $officeId, $sessionId);

            return [
                'status'  => $result['status'] ?? true,
                'message' => __('messages.chatbot_response_success'),
                'data'    => [
                    'reply'            => $result['reply'],
                    'referenced_cases' => $result['referenced_cases'] ?? [],
                ],
            ];
        } catch (\Exception $e) {
            Log::error('ChatbotService chat error: ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => $e->getMessage(),
                'data'    => [],
            ];
        }
    }

    /**
     * Get chatbot suggested legal questions.
     */
    public function getSuggestions(): array
    {
        try {
            $isAr = app()->getLocale() === 'ar';

            $suggestions = $isAr ? [
                "ما هي جلسات المحكمة المجدولة لليوم؟",
                "ما إجمالي المستحقات والأتعاب المتبقية لدى العملاء؟",
                "كم عدد القضايا المتداولة والنشطة حالياً بالمكتب؟",
                "كيف يمكنني إضافة عريضة دعوى جديدة وسند قبض؟",
                "صغ لي طلب تأجيل جلسة للاطلاع وإعادة الإعلان.",
            ] : [
                "What are today's scheduled court hearings?",
                "What is the total remaining unpaid legal fees from clients?",
                "How many active legal cases are currently in the office?",
                "How do I create a new legal case and financial receipt?",
                "Draft a request for court hearing postponement.",
            ];

            return [
                'status'  => true,
                'message' => __('messages.suggestions_retrieved_successfully'),
                'data'    => $suggestions,
            ];
        } catch (\Exception $e) {
            Log::error('ChatbotService getSuggestions error: ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => $e->getMessage(),
                'data'    => [],
            ];
        }
    }

    /**
     * Get paginated chat history for the lawyer/office.
     */
    public function getHistory(?int $userId, ?int $officeId, ?string $sessionId = null, int $perPage = 10): array
    {
        try {
            $query = ChatbotMessage::query();

            if ($userId) {
                $query->where('user_id', $userId);
            } elseif ($officeId) {
                $query->where('office_id', $officeId);
            } elseif ($sessionId) {
                $query->where('session_id', $sessionId);
            } else {
                return [
                    'status'  => false,
                    'message' => __('messages.no_chat_history_found'),
                    'data'    => collect(),
                ];
            }

            $messages = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return [
                'status'  => true,
                'message' => __('messages.history_retrieved_successfully'),
                'data'    => $messages,
            ];
        } catch (\Exception $e) {
            Log::error('ChatbotService getHistory error: ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => $e->getMessage(),
                'data'    => collect(),
            ];
        }
    }

    /**
     * Clear chat history.
     */
    public function clearHistory(?int $userId, ?int $officeId, ?string $sessionId = null): array
    {
        try {
            if ($userId) {
                ChatbotMessage::where('user_id', $userId)->delete();
            } elseif ($officeId) {
                ChatbotMessage::where('office_id', $officeId)->delete();
            } elseif ($sessionId) {
                ChatbotMessage::where('session_id', $sessionId)->delete();
            }

            return [
                'status'  => true,
                'message' => __('messages.history_cleared_successfully'),
            ];
        } catch (\Exception $e) {
            Log::error('ChatbotService clearHistory error: ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
