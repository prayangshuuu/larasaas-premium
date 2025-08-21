<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminDashboardController extends Controller
{
    /**
     * Admin dashboard.
     * NOTE: Access control is handled in routes (['auth','verified','admin']).
     */
    public function index()
    {
        $userCount     = User::count();
        $verifiedCount = User::whereNotNull('email_verified_at')->count();
        $twofaCount    = User::whereNotNull('two_factor_secret')->count();
        $recentUsers   = User::latest()
            ->take(8)
            ->get(['id', 'name', 'email', 'email_verified_at', 'created_at']);

        return view('admin.dashboard', compact(
            'userCount',
            'verifiedCount',
            'twofaCount',
            'recentUsers'
        ));
    }
}
