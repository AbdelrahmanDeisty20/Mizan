<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.otp_subject_' . ($type ?? 'register')) }}</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f6f8; margin: 0; padding: 20px; color: #333; }
        .card { max-width: 500px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; }
        .code { font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #1e293b; background: #f1f5f9; padding: 15px; text-align: center; border-radius: 6px; margin: 20px 0; }
        .footer { font-size: 12px; color: #64748b; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>{{ __('messages.welcome') }} {{ $name }}،</h2>
        <p>{{ __('messages.otp_message_' . ($type ?? 'register')) }}</p>
        <div class="code">{{ $code }}</div>
        <p>{{ __('messages.otp_expiry_notice') }}</p>
        <p>{{ __('messages.otp_ignore_notice') }}</p>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}.
        </div>
    </div>
</body>
</html>
