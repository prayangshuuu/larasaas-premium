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
    /**
     * Show the billing hub (Dashboard).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $subscription = $user->subscriptions()
            ->whereIn('status', ['active', 'past_due', 'canceled'])
            ->latest()
            ->first();

        // Fetch invoices
        $invoices = $request->user()->invoices()->latest()->get();

        return view('billing.index', [
            'subscription' => $subscription,
            'plan' => $subscription?->plan,
            'invoices' => $invoices,
        ]);
    }

    /**
     * Show available plans.
     */
    public function plans(Request $request)
    {
        $plans = Plan::where('is_active', true)->get();

        return view('billing.plans', [
            'plans' => $plans,
            'currentPlanId' => $request->user()->subscriptions()->where('status', 'active')->value('plan_id')
        ]);
    }


}
