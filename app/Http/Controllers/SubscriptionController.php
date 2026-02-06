<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\StripePaymentService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected $stripeService;

    public function __construct(StripePaymentService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Initiate checkout for a plan.
     */
    public function checkout(Request $request, Plan $plan)
    {
        $user = $request->user();

        // 1. Create Checkout Session
        // Note: Service handles customer creation and dynamic price creation if needed.
        $session = $this->stripeService->createCheckoutSession($user, $plan);

        // 2. Redirect to Stripe
        return redirect()->away($session->url);
    }

    /**
     * Cancel the current subscription.
     */
    public function cancel(Request $request)
    {
        $user = $request->user();

        try {
            $this->stripeService->cancelSubscription($user);
            return redirect()->route('dashboard')->with('success', 'Subscription cancelled successfully. It will remain active until the end of the period.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Unable to cancel subscription: ' . $e->getMessage());
        }
    }

    /**
     * Resume subscription (Optional placeholder).
     */
    public function resume(Request $request)
    {
        // Implementation for resuming would go here (e.g., Stripe update subscription to remove cancel_at_period_end)
        return redirect()->back()->with('info', 'Resume feature not yet implemented.');
    }
}
