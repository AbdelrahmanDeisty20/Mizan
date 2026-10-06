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

        if ($isAccepted) {
            return $next($request);
        }

        // Allow safe read methods (GET, HEAD, OPTIONS) for unaccepted users
        if ($request->isMethodSafe()) {
            return $next($request);
        }

        // Allow unaccepted users to update their phone number on profile update
        if ($request->is('api/profile/update') || $request->is('profile/update')) {
            $restrictedKeys = [
                'name', 'email', 'office_name', 'degree_id', 'governorate_id',
                'address', 'office_address', 'syndicate_card_id', 'trial_ends_at',
                'avatar', 'syndicate_card_image', 'image', 'type',
            ];

            $hasRestrictedFields = false;
            foreach ($restrictedKeys as $key) {
                if ($request->has($key) && ! is_null($request->input($key))) {
                    $hasRestrictedFields = true;
                    break;
                }
            }

            if (! $hasRestrictedFields) {
                return $next($request);
            }
        }

        return response()->json([
            'status'  => false,
            'message' => __('messages.account_not_accepted'),
        ], 403);
    }
}
