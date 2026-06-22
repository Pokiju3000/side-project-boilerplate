<?php

namespace PortfolioLocale\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetPortfolioLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locales = array_keys(config('portfolio-locale.locales', []));
        $locale = $request->session()->get('portfolio_locale')
            ?? $request->cookie('portfolio_locale')
            ?? config('app.locale');

        if (! in_array($locale, $locales, true)) {
            $locale = config('portfolio-locale.default', 'en');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
