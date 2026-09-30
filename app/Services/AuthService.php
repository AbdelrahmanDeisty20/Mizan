<?php

namespace App\Services;

use App\Http\Requests\API\LoginRequest;
use App\Http\Resources\UserResource;
use App\Mail\OtpMail;
use App\Models\Office;
use App\Models\Otp;
use App\Models\User;
use Hash;
use Mail;
use PHPUnit\Framework\MockObject\Stub\ReturnReference;

class AuthService
{
    /**
     * Create a new class instance.
     */
    public function register(array $data)
    {
        $office = Office::create([
            'office_name'       => $data['office_name'],
            'syndicate_card_id' => $data['syndicate_card_id'] ?? null,
            'degree_id'         => $data['degree_id'],
            'governorate_id'    => $data['governorate_id'],
            'office_address'    => $data['office_address'],
            'office_phone'      => $data['office_phone'] ?? $data['phone'] ?? null,
            'trial_ends_at'     => $data['trial_ends_at'] ?? null,
        ]);
        $avatarPath = null;
        if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
            $avatarPath = $data['avatar']->store('users/avatars', 'public');
        }

        $cardImagePath = null;
        if (isset($data['syndicate_card_image']) && $data['syndicate_card_image'] instanceof \Illuminate\Http\UploadedFile) {
            $cardImagePath = $data['syndicate_card_image']->store('users/syndicate_cards', 'public');
        }

        $user = User::create([
            'name'                 => $data['name'],
            'email'                => $data['email'],
            'phone'                => $data['phone'],
            'password'             => bcrypt($data['password']),
            'role'                 => 'lawyer',
            'client_code'          => null,
            'office_id'            => $office->id,
            'avatar'               => $avatarPath,
            'syndicate_card_image' => $cardImagePath,
        ]);

        $user->refresh();
        if ($user) {
            $code = random_int(100000, 999999);
            $codeHash = Hash::make((string) $code);
            Mail::to($user->email)->locale(app()->getLocale())->queue(new OtpMail($code, $user->name));
            Otp::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'phone' => $user->phone ?? null,
                'code' => $codeHash,
                'type' => 'register',
                'expires_at' => now()->addMinutes(5),
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => __('messages.user_registered_successfully'),
            'data' => new UserResource($user->load('office.degree', 'office.governorate')),
        ], 201);
    }
    public function login(array $data)
    {
        $user = null;

        if (! empty($data['login'])) {
            $identifier = $data['login'];
            $user = User::where('email', $identifier)
                ->orWhere('phone', $identifier)
                ->orWhereHas('office', function ($query) use ($identifier) {
                    $query->where('syndicate_card_id', $identifier)
                          ->orWhere('office_phone', $identifier);
                })->first();
        } elseif (! empty($data['email'])) {
            $user = User::where('email', $data['email'])->first();
        } elseif (! empty($data['phone'])) {
            $user = User::where('phone', $data['phone'])
                ->orWhereHas('office', function ($query) use ($data) {
                    $query->where('office_phone', $data['phone']);
                })->first();
        } elseif (! empty($data['syndicate_card_id'])) {
            $user = User::whereHas('office', function ($query) use ($data) {
                $query->where('syndicate_card_id', $data['syndicate_card_id']);
            })->first();
        }

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.invalid_credentials'),
            ], 401);
        }

        if (is_null($user->email_verified_at)) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.account_not_verified'),
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => __('messages.user_logged_in_successfully'),
            'token'   => $token,
            'data'    => new UserResource($user->load('office.degree', 'office.governorate')),
        ], 200);
    }

    public function verifyOtp(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (! $user) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.user_not_found'),
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.account_already_verified'),
            ], 400);
        }

        $otp = Otp::where('user_id', $user->id)
            ->where('type', 'register')
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $otp || ! Hash::check($data['code'], $otp->code)) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.otp_invalid'),
            ], 400);
        }

        $otp->update(['verified_at' => now()]);

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        return response()->json([
            'status'  => true,
            'message' => __('messages.otp_verified_successfully'),
            'data'    => new UserResource($user->load('office.degree', 'office.governorate')),
        ], 200);
    }

    public function resendOtp(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (! $user) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.user_not_found'),
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.account_already_verified'),
            ], 400);
        }

        $recentOtp = Otp::where('user_id', $user->id)
            ->where('type', 'register')
            ->where('created_at', '>', now()->subMinute())
            ->first();

        if ($recentOtp) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.otp_cooldown_active'),
            ], 429);
        }

        $code = random_int(100000, 999999);
        $codeHash = Hash::make((string) $code);

        Mail::to($user->email)->locale(app()->getLocale())->queue(new OtpMail($code, $user->name, 'resend'));

        Otp::create([
            'user_id'    => $user->id,
            'email'      => $user->email,
            'phone'      => $user->phone ?? null,
            'code'       => $codeHash,
            'type'       => 'register',
            'expires_at' => now()->addMinutes(5),
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('messages.otp_sent_successfully'),
        ], 200);
    }

    /**
     * Get authenticated user profile.
     */
    public function profile()
    {
        $user = auth()->user();

        if (! $user) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.user_not_found'),
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => __('messages.profile_retrieved_successfully'),
            'data'    => new UserResource($user->load('office.degree', 'office.governorate')),
        ], 200);
    }

    /**
     * Update authenticated user profile.
     */
    public function updateProfile(array $data)
    {
        $user = auth()->user();

        if (! $user) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.user_not_found'),
            ], 404);
        }

        $userData = array_filter([
            'name'  => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
        ], fn ($val) => ! is_null($val));

        if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
            if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $userData['avatar'] = $data['avatar']->store('users/avatars', 'public');
        }

        if (isset($data['syndicate_card_image']) && $data['syndicate_card_image'] instanceof \Illuminate\Http\UploadedFile) {
            if ($user->syndicate_card_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->syndicate_card_image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->syndicate_card_image);
            }
            $userData['syndicate_card_image'] = $data['syndicate_card_image']->store('users/syndicate_cards', 'public');
        }

        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile && ! empty($data['type'])) {
            $type = $data['type'];
            if ($type === 'avatar') {
                if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
                }
                $userData['avatar'] = $data['image']->store('users/avatars', 'public');
            } elseif ($type === 'syndicate_card' || $type === 'syndicate_card_image') {
                if ($user->syndicate_card_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->syndicate_card_image)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->syndicate_card_image);
                }
                $userData['syndicate_card_image'] = $data['image']->store('users/syndicate_cards', 'public');
            }
        }

        if (! empty($userData)) {
            $user->update($userData);
        }


        if ($user->office) {
            $officeData = array_filter([
                'office_name'       => $data['office_name'] ?? null,
                'syndicate_card_id' => $data['syndicate_card_id'] ?? null,
                'degree_id'         => $data['degree_id'] ?? null,
                'governorate_id'    => $data['governorate_id'] ?? null,
                'office_address'    => $data['office_address'] ?? null,
                'office_phone'      => $data['office_phone'] ?? null,
            ], fn ($val) => ! is_null($val));

            if (! empty($officeData)) {
                $user->office->update($officeData);
            }
        }

        return response()->json([
            'status'  => true,
            'message' => __('messages.profile_updated_successfully'),
            'data'    => new UserResource($user->fresh()->load('office.degree', 'office.governorate')),
        ], 200);
    }
}

