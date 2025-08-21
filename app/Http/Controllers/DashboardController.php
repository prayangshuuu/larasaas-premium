<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the appropriate dashboard based on user role.
     */
    public function index()
    {
        // Routes already use 'auth' middleware; this ensures safety if called elsewhere
        $user = Auth::user();

        // Admins go to the dedicated admin dashboard (protected by gate/middleware)
        if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Non-admins see the regular user dashboard
        return view('dashboard');
    }
}
