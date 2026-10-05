<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ChatRequest;
use App\Http\Resources\ChatbotMessageResource;
use App\Services\ChatbotService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    use ApiResponse;

    public function __construct(protected ChatbotService $chatbotService) {}

    /**
     * Send prompt to Groq AI Legal Assistant and get intelligent office & legal response.
     */
    public function chat(ChatRequest $request): JsonResponse
    {
        $user     = auth('sanctum')->user();
        $userId   = $user?->id;
        $officeId = $user?->office_id;

        $result = $this->chatbotService->chat($userId, $officeId, $request->validated());

        if (! $result['status']) {
            return response()->json([
                'status'  => false,
                'message' => $result['message'],
            ], 400);
        }

        return response()->json([
            'status'  => true,
            'message' => $result['message'],
            'data'    => $result['data'],
        ], 200);
    }

    /**
     * Get quick legal suggestion chips for chatbot UI.
     */
    public function suggestions(): JsonResponse
    {
        $result = $this->chatbotService->getSuggestions();

        if (! $result['status']) {
            return response()->json([
                'status'  => false,
                'message' => $result['message'],
            ], 400);
        }

        return response()->json([
            'status'  => true,
            'message' => $result['message'],
            'data'    => $result['data'],
        ], 200);
    }

    /**
     * Get paginated chat history for the authenticated user/office.
     */
    public function history(Request $request): JsonResponse
    {
        $user      = auth('sanctum')->user();
        $userId    = $user?->id;
        $officeId  = $user?->office_id;
        $sessionId = $request->input('session_id') ?? $request->header('X-Session-ID');
        $perPage   = (int) $request->input('per_page', 10);

        $result = $this->chatbotService->getHistory($userId, $officeId, $sessionId, $perPage);

        if (! $result['status']) {
            return response()->json([
                'status'  => false,
                'message' => $result['message'],
            ], 400);
        }

        return $this->paginated(
            ChatbotMessageResource::class,
            $result['data'],
            $result['message']
        );
    }

    /**
     * Clear chat history for the authenticated user/office.
     */
    public function clearHistory(Request $request): JsonResponse
    {
        $user      = auth('sanctum')->user();
        $userId    = $user?->id;
        $officeId  = $user?->office_id;
        $sessionId = $request->input('session_id') ?? $request->header('X-Session-ID');

        $result = $this->chatbotService->clearHistory($userId, $officeId, $sessionId);

        if (! $result['status']) {
            return response()->json([
                'status'  => false,
                'message' => $result['message'],
            ], 400);
        }

        return response()->json([
            'status'  => true,
            'message' => $result['message'],
            'data'    => [],
        ], 200);
    }
}
