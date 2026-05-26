<?php

use App\Http\Controllers\Admin\HealthDiagnosticController;
use App\Http\Controllers\Auth\AzureAuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AzureAuthController::class, 'showLogin'])->name('login');
    Route::get('/auth/azure', [AzureAuthController::class, 'redirectToAzure'])->name('azure.login');
    Route::get('/auth/azure/callback', [AzureAuthController::class, 'handleAzureCallback'])->name('azure.callback');
    Route::post('/auth/demo', [AzureAuthController::class, 'loginAsDemoUser'])->name('demo.login');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AzureAuthController::class, 'logout'])->name('logout');

    Route::middleware('azure.access')->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::get('/admin/diagnostic', [HealthDiagnosticController::class, 'index'])
            ->middleware('azure.admin')
            ->name('admin.diagnostics');
    });
});
