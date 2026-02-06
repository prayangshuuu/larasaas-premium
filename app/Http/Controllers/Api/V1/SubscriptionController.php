<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Initiate a checkout session.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($request->plan_id);
        $user = $request->user();

        // Ensure customer exists in Stripe
        $user->createOrGetStripeCustomer();

        // Create Checkout Session
        // Note: For API, we return a URL instead of redirecting
        $checkout = $user->newSubscription('default', $plan->stripe_id)
            ->checkout([
                'success_url' => route('billing.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('billing.cancel-return'),
            ]);

        return response()->json([
            'checkout_url' => $checkout->url,
        ]);
    }

    /**
     * Cancel the active subscription.
     */
    public function cancel(Request $request)
    {
        $user = $request->user();

        if ($user->subscription('default') && $user->subscription('default')->active()) {
            $user->subscription('default')->cancel();
            return response()->json(['message' => 'Subscription canceled successfully.']);
        }

        return response()->json(['message' => 'No active subscription to cancel.'], 400);
    }

    /**
     * Resume a canceled subscription.
     */
    public function resume(Request $request)
    {
        $user = $request->user();

        if ($user->subscription('default') && $user->subscription('default')->onGracePeriod()) {
            $user->subscription('default')->resume();
            return response()->json(['message' => 'Subscription resumed successfully.']);
        }

        return response()->json(['message' => 'Unable to resume subscription.'], 400);
    }
}
