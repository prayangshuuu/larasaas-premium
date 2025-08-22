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

/*
|--------------------------------------------------------------------------
| Public landing page
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| User dashboard (non-admin)
| DashboardController redirects admins to admin.dashboard.
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

    // Persist theme choice
    Route::post('/theme', [ThemeController::class, 'update'])->name('theme.update');

    // User-generated “share access code” (for consented impersonation)
    Route::post('/impersonation-code',   [ImpersonationCodeController::class, 'store'])->name('impersonation.code.store');
    Route::delete('/impersonation-code', [ImpersonationCodeController::class, 'destroy'])->name('impersonation.code.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin area
| Requires: auth + verified + admin + not-banned + impersonation guard
| Prefix: /admin   Names: admin.*
|--------------------------------------------------------------------------
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
        | Names aligned to Blade:
        |   admin.settings.index
        |   admin.settings.app.update
        |   admin.settings.app.logo
        |   admin.settings.smtp.update
        |   admin.settings.features.update
        */
        Route::prefix('settings')->as('settings.')->controller(SystemSettingsController::class)->group(function () {
            Route::get('/',            'index')->name('index');                 // GET  /admin/settings
            Route::post('/app',        'updateApp')->name('app.update');        // POST /admin/settings/app
            Route::post('/app/logo',   'uploadLogo')->name('app.logo');         // POST /admin/settings/app/logo
            Route::post('/smtp',       'updateSmtp')->name('smtp.update');      // POST /admin/settings/smtp
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
        | Impersonation (feature-flagged; MFA here only)
        |------------------------------------------------------------------
        */
        if (config('features.impersonation', false)) {
            Route::prefix('impersonate')
                ->as('impersonate.')
                ->controller(ImpersonationController::class)
                ->middleware('admin.mfa')
                ->group(function () {
                    Route::post('/start/{user}', 'start')->name('start'); // begin impersonation
                    Route::post('/stop',         'stop')->name('stop');   // end impersonation
                });
        }
    });

/*
|--------------------------------------------------------------------------
| Auth scaffolding
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
