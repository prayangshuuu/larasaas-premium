<?php

use Laravel\Fortify\Features;

return [

    /*
    |--------------------------------------------------------------------------
    | Fortify Guard
    |--------------------------------------------------------------------------
    */
    'guard' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Fortify Password Broker
    |--------------------------------------------------------------------------
    */
    'passwords' => 'users',

    /*
    |--------------------------------------------------------------------------
    | Username / Email
    |--------------------------------------------------------------------------
    | Fortify expects "email" by default. Keep this unless you implement
    | custom username login everywhere.
    */
    'username' => 'email',
    'email'    => 'email',

    /*
    |--------------------------------------------------------------------------
    | Lowercase Usernames
    |--------------------------------------------------------------------------
    */
    'lowercase_usernames' => true,

    /*
    |--------------------------------------------------------------------------
    | Home Path (post-login redirect)
    |--------------------------------------------------------------------------
    */
    'home' => '/dashboard',

    /*
    |--------------------------------------------------------------------------
    | Fortify Routes Prefix / Subdomain
    |--------------------------------------------------------------------------
    */
    'prefix'  => '',
    'domain'  => null,

    /*
    |--------------------------------------------------------------------------
    | Fortify Routes Middleware
    |--------------------------------------------------------------------------
    */
    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'limiters' => [
        'login'       => 'login',
        'two-factor'  => 'two-factor',
    ],

    /*
    |--------------------------------------------------------------------------
    | Register View Routes
    |--------------------------------------------------------------------------
    | Keep "true" so your Blade views (DaisyUI) render for auth screens.
    */
    'views' => true,

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    | Email verification is ON. 2FA is ON with confirmation and
    | password confirmation (matches your “confirm code” flow).
    */
    'features' => [
        Features::registration(),
        Features::resetPasswords(),
        Features::emailVerification(),          // ← ENABLED
        Features::updateProfileInformation(),
        Features::updatePasswords(),
        Features::twoFactorAuthentication([
            'confirm'         => true,          // ask to confirm TOTP after enabling
            'confirmPassword' => true,          // confirm password before enable/disable
            // 'window' => 0,                   // optional: strict clock tolerance
        ]),
    ],
];
