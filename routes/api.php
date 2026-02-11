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
    
    // Public Plans
    Route::get('/plans', [\App\Http\Controllers\Api\V1\PlanController::class, 'index'])->name('plans.index');


    /* ---------- Stripe Webhook ---------- */
    Route::post('/stripe/webhook', [\App\Http\Controllers\Webhook\StripeWebhookController::class, 'handle'])->name('stripe.webhook');

    /* ---------- Authenticated (Sanctum) ---------- */
    Route::middleware('auth:sanctum')->group(function () {

        // Current user profile
        Route::get('/me', [ApiProfileController::class, 'show'])->name('me.show');
        Route::put('/me', [ApiProfileController::class, 'update'])->name('me.update');

        // User Invoices
        Route::get('/invoices', [\App\Http\Controllers\Api\V1\InvoiceController::class, 'index'])->name('invoices.index');

        Route::get('/invoices/{invoice}', [\App\Http\Controllers\Api\V1\InvoiceController::class, 'show'])->name('invoices.show');

        // Subscription Management
        Route::get('/subscriptions/current', [\App\Http\Controllers\Api\V1\SubscriptionController::class, 'current'])->name('subscriptions.current');
        Route::post('/subscriptions/checkout', [\App\Http\Controllers\Api\V1\SubscriptionController::class, 'checkout'])->name('subscriptions.checkout');
        Route::post('/subscriptions/cancel', [\App\Http\Controllers\Api\V1\SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
        Route::post('/subscriptions/resume', [\App\Http\Controllers\Api\V1\SubscriptionController::class, 'resume'])->name('subscriptions.resume');

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


            // Subscription Plans (Admin)
            Route::apiResource('plans', \App\Http\Controllers\Api\V1\Admin\PlanController::class);

            // System Settings (Subscription Module)
            Route::post('settings/subscription', [\App\Http\Controllers\Api\V1\Admin\SubscriptionSettingsController::class, 'update'])->name('settings.subscription.update');
            Route::put('system-settings', [\App\Http\Controllers\Admin\SystemSettingController::class, 'update'])->name('system-settings.update');
            Route::get('system-settings/{key}', [\App\Http\Controllers\Admin\SystemSettingController::class, 'show'])->name('system-settings.show');
        });

        /*
        |--------------------------------------------------------------------------
        | Impersonation Stop (API)
        |--------------------------------------------------------------------------
        | Must be accessible to non-admins (the impersonated user).
        */
        Route::middleware(['not-banned'])
            ->prefix('admin/impersonate')
            ->name('admin.impersonate.')
            ->group(function() {
                 Route::match(['get', 'post'], '/stop', [ApiAdminImpersonationController::class, 'stop'])->name('stop');
            });
    });
});
