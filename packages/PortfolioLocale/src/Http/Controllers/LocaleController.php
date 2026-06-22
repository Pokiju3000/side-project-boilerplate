<?php

namespace PortfolioLocale\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class LocaleController extends Controller
{
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        abort_unless(array_key_exists($locale, config('portfolio-locale.locales', [])), 404);

        App::setLocale($locale);
        session(['portfolio_locale' => $locale]);
        Cookie::queue('portfolio_locale', $locale, 60 * 24 * 365);

        return redirect()->back(fallback: url('/'));
    }
}
