<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingController extends Controller
{
    /**
     * Display the billing hub.
     * Checks if the user is subscribed and returns the appropriate view.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // If user is subscribed (valid subscription)
        if ($user->subscribed('default')) {
            $subscription = $user->subscription('default');
            $plan = Plan::where('stripe_id', $subscription->stripe_price)->first();
            
            return view('billing.manage', [
                'subscription' => $subscription,
                'plan' => $plan, // Might be null if plan was deleted locally but active in Stripe
                'invoices' => $user->invoices(), // Pass invoices to manage view directly or link to invoice index
            ]);
        }

        // If user is NOT subscribed, show plans
        $plans = Plan::where('is_active', true)->get();

        return view('billing.plans', [
            'plans' => $plans,
        ]);
    }
}
