<?php

namespace PortfolioLocale;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use PortfolioLocale\Http\Middleware\SetPortfolioLocale;

class PortfolioLocaleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/portfolio-locale.php', 'portfolio-locale');
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'portfolio-locale');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'portfolio-locale');

        Route::pushMiddlewareToGroup('web', SetPortfolioLocale::class);
    }
}
