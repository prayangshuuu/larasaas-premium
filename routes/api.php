<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\ProfileController as ApiProfileController;
use App\Http\Controllers\Api\V1\Admin\UsersController as ApiAdminUsersController;
use App\Http\Controllers\Api\V1\Admin\SystemSettingsController as ApiAdminSettingsController;
use App\Http\Controllers\Api\V1\Admin\AuditController as ApiAdminAuditController;
use App\Http\Controllers\Api\V1\Admin\ImpersonationController as ApiAdminImpersonationController;

Route::prefix('v1')->name('api.v1.')->group(function () {

    /* ---------- Public ping ---------- */
    Route::get('/ping', fn () => response()->json(['ok' => true, 'time' => now()]));

    /* ---------- Stripe Webhook ---------- */
    Route::post('/stripe/webhook', [\App\Http\Controllers\Webhook\StripeWebhookController::class, 'handle'])->name('stripe.webhook');

    /* ---------- Authenticated (Sanctum) ---------- */
    Route::middleware('auth:sanctum')->group(function () {

        // Current user profile
        Route::get('/me', [ApiProfileController::class, 'show'])->name('me.show');
        Route::put('/me', [ApiProfileController::class, 'update'])->name('me.update');

        // User Invoices
        Route::get('/invoices', [\App\Http\Controllers\User\InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [\App\Http\Controllers\User\InvoiceController::class, 'show'])->name('invoices.show');

        /* ----- Admin area (admin + not-banned + impersonation guard) ----- */
        Route::middleware(['admin', 'not-banned', 'impersonation'])->prefix('admin')->name('admin.')->group(function () {

            // Users (CRUD)
            Route::apiResource('users', ApiAdminUsersController::class);

            // Audit logs (read-only)
            Route::get('audit', [ApiAdminAuditController::class, 'index'])->name('audit.index');
            Route::get('audit/{log}', [ApiAdminAuditController::class, 'show'])->name('audit.show');

            // Settings
            Route::get('settings', [ApiAdminSettingsController::class, 'index'])->name('settings.index');
            Route::get('settings/{key}', [ApiAdminSettingsController::class, 'show'])->name('settings.show');
            Route::put('settings/{key}', [ApiAdminSettingsController::class, 'update'])->name('settings.update');

            // Logo upload (multipart/form-data)
            Route::post('settings/logo', [ApiAdminSettingsController::class, 'uploadLogo'])->name('settings.logo');

            // Impersonation
            Route::post('impersonate/start/{user}', [ApiAdminImpersonationController::class, 'start'])
                ->middleware(['admin.mfa', 'feature:features.impersonation'])
                ->name('impersonate.start');

            // STOP is always available to exit impersonation
            Route::post('impersonate/stop', [ApiAdminImpersonationController::class, 'stop'])
                ->name('impersonate.stop');

            // Subscription Plans
            Route::apiResource('plans', \App\Http\Controllers\Admin\PlanController::class);

            // System Settings (Subscription Module)
            Route::put('system-settings', [\App\Http\Controllers\Admin\SystemSettingController::class, 'update'])->name('system-settings.update');
            Route::get('system-settings/{key}', [\App\Http\Controllers\Admin\SystemSettingController::class, 'show'])->name('system-settings.show');
        });
    });
});
