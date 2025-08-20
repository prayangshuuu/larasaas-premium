<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the appropriate dashboard based on user role.
     */
    public function index()
    {
        // Check if the authenticated user is an admin
        if (Auth::user()->isAdmin()) {
            // If yes, get the count of all users
            $userCount = User::count();
            // and return the admin dashboard view with the user count
            return view('admindashboard', ['userCount' => $userCount]);
        }

        // If not an admin, return the default user dashboard
        return view('dashboard');
    }
}
