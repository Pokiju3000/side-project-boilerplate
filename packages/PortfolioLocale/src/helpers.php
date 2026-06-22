<?php

use Illuminate\Support\Facades\Lang;

if (! function_exists('portfolio_text')) {
    function portfolio_text(string $key, ?string $fallback = null): string
    {
        $translationKey = 'portfolio.'.$key;

        return Lang::has($translationKey) ? __($translationKey) : (string) ($fallback ?? $key);
    }
}
