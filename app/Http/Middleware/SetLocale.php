<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Admin always uses default locale (vi) — skip locale switching
        if ($request->is('admin') || $request->is('admin/*')) {
            app()->setLocale(config('translatable.default', 'vi'));
            return $next($request);
        }

        $locales = config('translatable.locales', ['vi', 'en']);
        $default = config('translatable.default', 'vi');

        // Priority: session > cookie > default
        // Note: We do NOT use browser Accept-Language because it would
        // set locale to 'en' for most browsers, but content only has 'vi'
        $locale = session('locale')
            ?? $request->cookie('locale')
            ?? $default;

        // Validate locale
        if (!in_array($locale, $locales)) {
            $locale = $default;
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
