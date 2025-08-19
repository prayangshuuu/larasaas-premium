<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class FortifyServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Register login view
        Fortify::loginView(fn () => view('auth.login'));

        // Register registration view
        Fortify::registerView(fn () => view('auth.register'));

        // Register password reset view
        Fortify::requestPasswordResetLinkView(fn () => view('auth.forgot-password'));
        Fortify::resetPasswordView(fn ($request) => view('auth.reset-password', ['request' => $request]));

        // Create user
        Fortify::createUsersUsing(function ($input) {
            Validator::make($input, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ])->validate();

            return User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);
        });

        // Update user password
        Fortify::updateUserPasswordsUsing(\Laravel\Fortify\Actions\UpdateUserPassword::class);
    }
}
