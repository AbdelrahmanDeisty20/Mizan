<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Return a success JSON response.
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $code
     * @return JsonResponse
     */
    public function success($data = [], $message = 'success', $code = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return a 201 Created JSON response.
     *
     * @param mixed $data
     * @param string $message
     * @return JsonResponse
     */
    public function created($data = [], $message = 'Resource created successfully'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    /**
     * Return a success message for deleted resources.
     *
     * @param string $message
     * @return JsonResponse
     */
    public function deleted($message = 'Resource deleted successfully'): JsonResponse
    {
        return $this->success([], $message, 200);
    }

    /**
     * Return a message-only JSON response (no data field).
     *
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function messageOnly($message, $code = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
        ], $code);
    }

    /**
     * Return an error JSON response.
     *
     * @param string|null $message
     * @param int $code
     * @param mixed $errors
     * @return JsonResponse
     */
    public function error($message = 'Error occurred', $code = 400, $errors = []): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Return a 404 Not Found JSON response.
     *
     * @param string $message
     * @return JsonResponse
     */
    public function notFound($message = 'Resource not found'): JsonResponse
    {
        return $this->error($message, 404);
    }

    /**
     * Return a 401 Unauthorized JSON response.
     *
     * @param string $message
     * @return JsonResponse
     */
    public function unauthorized($message = 'Unauthenticated'): JsonResponse
    {
        return $this->error($message, 401);
    }

    /**
     * Return a 403 Forbidden JSON response.
     *
     * @param string $message
     * @return JsonResponse
     */
    public function forbidden($message = 'Forbidden'): JsonResponse
    {
        return $this->error($message, 403);
    }

    /**
     * Return a paginated JSON response.
     *
     * @param string $resourceClass
     * @param mixed $paginator
     * @param string $message
     * @param array $extra
     * @return JsonResponse
     */
    public function paginated($resourceClass, $paginator, $message = 'success', array $extra = []): JsonResponse
    {
        $response = [
            'status' => true,
            'message' => $message,
            'data' => $resourceClass::collection($paginator->items()),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ];

        if (!empty($extra)) {
            $response = array_merge($response, $extra);
        }

        return response()->json($response);
    }
}
