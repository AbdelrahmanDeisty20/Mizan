<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccepted
{
    /**
     * Handle an incoming request.
     * Checks if the authenticated user is accepted/verified.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 401);
        }

        $isAccepted = (bool) ($user->is_accepted ?? ! is_null($user->email_verified_at));

        if (! $isAccepted) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.account_not_accepted'),
            ], 403);
        }

        return $next($request);
    }
}
