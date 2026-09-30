<?php

namespace App\Http\Middleware;

use App;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class SetLang
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $currentLocale = session('lang') ?? $request->cookie('lang');
        $locale = $request->header('lang')
            ?? $request->query('lang')
            ?? $currentLocale
            ?? $request->header('Accept-Language')
            ?? config('app.locale');

        // Clean and confirm language
        $locale = substr($locale, 0, 2);  // Take first two characters
        $supportedLocales = ['en', 'ar'];

        if (in_array($locale, $supportedLocales)) {
            App::setLocale($locale);
            if (session('lang') !== $locale) {
                session(['lang' => $locale]);
            }
            if ($request->cookie('lang') !== $locale) {
                cookie()->queue('lang', $locale, 60 * 24 * 365);
            }
        }
        return $next($request);
    }
}
