<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgetPasswordService
{
    public function sendOtp(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (! $user) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.user_not_found'),
            ], 404);
        }

        $recentOtp = Otp::where('user_id', $user->id)
            ->where('type', 'reset_password')
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

        Mail::to($user->email)->locale(app()->getLocale())->queue(new OtpMail($code, $user->name, 'reset_password'));

        Otp::create([
            'user_id'    => $user->id,
            'email'      => $user->email,
            'phone'      => $user->phone ?? null,
            'code'       => $codeHash,
            'type'       => 'reset_password',
            'expires_at' => now()->addMinutes(5),
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('messages.otp_sent_successfully'),
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

        $otp = Otp::where('user_id', $user->id)
            ->where('type', 'reset_password')
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

        $token = Str::random(60);
        $otp->update([
            'verified_at' => now(),
            'reset_token' => $token,
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('messages.otp_verified_successfully'),
            'token'   => $token,
        ], 200);
    }

    public function resetPassword(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (! $user) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.user_not_found'),
            ], 404);
        }

        $otp = Otp::where('user_id', $user->id)
            ->where('type', 'reset_password')
            ->whereNotNull('verified_at')
            ->where('reset_token', $data['token'])
            ->first();

        if (! $otp) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.invalid_token'),
            ], 400);
        }

        $user->update([
            'password' => bcrypt($data['password']),
        ]);

        $otp->delete();

        return response()->json([
            'status'  => true,
            'message' => __('messages.password_reset_successfully'),
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

        $recentOtp = Otp::where('user_id', $user->id)
            ->where('type', 'reset_password')
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

        Mail::to($user->email)->locale(app()->getLocale())->queue(new OtpMail($code, $user->name, 'reset_password'));

        Otp::create([
            'user_id'    => $user->id,
            'email'      => $user->email,
            'phone'      => $user->phone ?? null,
            'code'       => $codeHash,
            'type'       => 'reset_password',
            'expires_at' => now()->addMinutes(5),
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('messages.otp_sent_successfully'),
        ], 200);
    }
}
