<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ThemeController;

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\ImpersonationController;
use App\Http\Controllers\Admin\SystemSettingsController;

use App\Http\Controllers\ImpersonationCodeController;
use App\Http\Controllers\TwoFactorRecoveryCodesController;
use App\Http\Controllers\Auth\SocialiteController;

/*
|--------------------------------------------------------------------------
| Public landing page
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'));

/*
|--------------------------------------------------------------------------
| Social Login (Google)
| Requires: config/services.php 'google' + .env GOOGLE_* + SocialiteController
|--------------------------------------------------------------------------
*/
Route::get('/auth/google/redirect', [SocialiteController::class, 'redirect'])
    ->name('oauth.google.redirect');
Route::get('/auth/google/callback', [SocialiteController::class, 'callback'])
    ->name('oauth.google.callback');

/*
|--------------------------------------------------------------------------
| User dashboard (non-admin)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'not-banned'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Authenticated user routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'not-banned'])->group(function () {
    // Profile
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Theme (toggle light/dark etc.)
    Route::post('/theme', [ThemeController::class, 'update'])->name('theme.update');

    // 2FA recovery codes (guarded by recent password confirmation)
    Route::get('/two-factor/recovery-codes', [TwoFactorRecoveryCodesController::class, 'show'])
        ->middleware('password.confirm')
        ->name('two-factor.codes.show');
    Route::post('/two-factor/recovery-codes', [TwoFactorRecoveryCodesController::class, 'regenerate'])
        ->middleware('password.confirm')
        ->name('2fa.codes.regenerate');
    Route::get('/two-factor/recovery-codes/download', [TwoFactorRecoveryCodesController::class, 'download'])
        ->middleware('password.confirm')
        ->name('2fa.codes.download');

    // Admin requests user consent code for impersonation (per-user)
    Route::post('/impersonation-code',   [ImpersonationCodeController::class, 'store'])->name('impersonation.code.store');
    Route::delete('/impersonation-code', [ImpersonationCodeController::class, 'destroy'])->name('impersonation.code.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin area
| Requires: auth + verified + admin + not-banned + impersonation guard
| Prefix: /admin   Names: admin.*
|--------------------------------------------------------------------------
|
| Impersonation notes:
| - Guard 'impersonation' blocks ALL admin.* while impersonating (read-only unless 'full'),
|   except admin.impersonate.stop which is always allowed so you can exit.
*/
Route::middleware(['auth', 'verified', 'admin', 'not-banned', 'impersonation'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        // Default redirect
        Route::redirect('/', '/admin/dashboard');

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        /*
        |------------------------------------------------------------------
        | System Settings (App / SMTP / Features)
        |------------------------------------------------------------------
        */
        Route::prefix('settings')
            ->as('settings.')
            ->controller(SystemSettingsController::class)
            ->group(function () {
                // Main page
                Route::get('/', 'index')->name('index'); // GET /admin/settings

                // Friendly GET fallbacks → index (prevents 405s if someone visits these URLs)
                Route::get('/features', fn () => redirect()->route('admin.settings.index'))->name('features');
                Route::get('/app',      fn () => redirect()->route('admin.settings.index'))->name('app');
                Route::get('/smtp',     fn () => redirect()->route('admin.settings.index'))->name('smtp');

                // Actual update endpoints (POST)
                Route::post('/app',        'updateApp')->name('app.update');           // POST /admin/settings/app
                Route::post('/app/logo',   'uploadLogo')->name('app.logo');            // POST /admin/settings/app/logo
                Route::post('/smtp',       'updateSmtp')->name('smtp.update');         // POST /admin/settings/smtp
                Route::post('/features',   'updateFeatures')->name('features.update'); // POST /admin/settings/features
            });

        /*
        |------------------------------------------------------------------
        | Users management (CRUD + actions + bulk + export)
        |------------------------------------------------------------------
        */
        Route::prefix('users')->as('users.')->controller(UserController::class)->group(function () {
            Route::get('/',            'index')->name('index');
            Route::get('/create',      'create')->name('create');
            Route::post('/',           'store')->name('store');
            Route::get('/{user}/edit', 'edit')->name('edit');
            Route::put('/{user}',      'update')->name('update');
            Route::delete('/{user}',   'destroy')->name('destroy');

            // Single-user actions
            Route::post('/{user}/ban',     'ban')->name('ban');
            Route::post('/{user}/unban',   'unban')->name('unban');
            Route::post('/{user}/promote', 'promote')->name('promote');
            Route::post('/{user}/demote',  'demote')->name('demote');

            // Bulk actions (ids[]; action=ban|unban|delete|promote|demote)
            Route::post('/bulk', 'bulk')->name('bulk');

            // Export CSV
            Route::get('/export/csv', 'exportCsv')->name('export.csv');
        });

        /*
        |------------------------------------------------------------------
        | Audit logs
        |------------------------------------------------------------------
        */
        Route::prefix('audit')->as('audit.')->controller(AuditController::class)->group(function () {
            Route::get('/', 'index')->name('index'); // /admin/audit
        });

        /*
        |------------------------------------------------------------------
        | Impersonation
        |------------------------------------------------------------------
        | START requires admin MFA and feature flag.
        | STOP is always available so you can exit impersonation even if feature was turned off.
        */
        Route::prefix('impersonate')
            ->as('impersonate.')
            ->controller(ImpersonationController::class)
            ->group(function () {
                Route::post('/start/{user}', 'start')
                    ->middleware(['admin.mfa', 'feature:features.impersonation'])
                    ->name('start');

                Route::post('/stop', 'stop')
                    ->name('stop'); // no 'feature' / 'admin.mfa' on purpose
            });
    });

/*
|--------------------------------------------------------------------------
| Auth scaffolding
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
