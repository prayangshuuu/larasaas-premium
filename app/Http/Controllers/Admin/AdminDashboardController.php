<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
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

        // Billing Stats
        $totalRevenue = \App\Models\Transaction::paid()->sum('amount');
        $activeSubscribers = \App\Models\Subscription::where('status', 'active')->count();

        // New Stats
        $newUsersCount = User::where('created_at', '>=', now()->subDays(30))->count();
        
        $pendingTicketsCount = 0;
        if (\App\Helpers\Feature::enabled('support_enabled')) {
            $pendingTicketsCount = \App\Models\SupportTicket::whereIn('status', ['open', 'pending'])->count();
        }

        // Pending Bkash Payments
        $bkashEnabled = (bool) SystemSetting::where('key', 'bkash_enabled')->value('value');
        $pendingPaymentsCount = 0;
        if ($bkashEnabled) {
            $pendingPaymentsCount = \App\Models\Transaction::where('status', 'pending')
                ->where('payment_method', 'bkash_manual')
                ->count();
        }

        return view('admin.dashboard', compact(
            'userCount',
            'verifiedCount',
            'twofaCount',
            'recentUsers',
            'totalRevenue',
            'activeSubscribers',
            'newUsersCount',
            'pendingTicketsCount',
            'bkashEnabled',
            'pendingPaymentsCount'
        ));
    }
}
