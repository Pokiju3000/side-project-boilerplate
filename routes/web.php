<?php

use App\Http\Controllers\Admin\HealthDiagnosticController;
use App\Http\Controllers\Auth\AzureAuthController;
use App\Http\Controllers\DeliveryOpsController;
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
        Route::get('/dashboard', [DeliveryOpsController::class, 'dashboard'])->name('dashboard');
        Route::get('/projects', [DeliveryOpsController::class, 'projects'])->name('projects.index');
        Route::get('/projects/{project}', [DeliveryOpsController::class, 'project'])->name('projects.show');
        Route::get('/validation', [DeliveryOpsController::class, 'validation'])->name('operations.validation');
        Route::get('/imports', [DeliveryOpsController::class, 'imports'])->name('operations.imports');
        Route::get('/resources', [DeliveryOpsController::class, 'resources'])->name('operations.resources');

        Route::get('/admin/diagnostic', [HealthDiagnosticController::class, 'index'])
            ->middleware('azure.admin')
            ->name('admin.diagnostics');
    });
});
