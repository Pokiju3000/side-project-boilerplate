<?php

use Illuminate\Support\Facades\Route;
use PortfolioLocale\Http\Controllers\LocaleController;

Route::middleware('web')->get('/locale/{locale}', LocaleController::class)->name('locale.switch');
