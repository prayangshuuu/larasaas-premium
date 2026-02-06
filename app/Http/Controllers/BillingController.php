<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\StripePaymentService;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    /**
     * Redirect to Stripe Billing Portal.
     */
    public function portal(Request $request, StripePaymentService $paymentService)
    {
        $user = $request->user();
        
        try {
            $session = $paymentService->createBillingPortalSession($user);
            return redirect($session->url);
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to access billing portal: ' . $e->getMessage());
        }
    }
    /**
     * Show the billing hub.
     * If subscribed, show management view.
     * If not, show plans.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Check for active subscription
        // We consider 'active' or 'canceled' (grace period) as having a subscription to manage.
        // Assuming single subscription logic for now.
        $subscription = $user->subscriptions()
            ->whereIn('status', ['active', 'past_due', 'canceled'])
            ->latest()
            ->first();

        if ($subscription && $subscription->current_period_end > now()) {
            // User is subscribed or on grace period
            return view('billing.manage', [
                'subscription' => $subscription,
                'plan' => $subscription->plan,
            ]);
        }

        // Not subscribed or expired
        $plans = Plan::where('is_active', true)->get();

        return view('billing.plans', [
            'plans' => $plans,
        ]);
    }
}
