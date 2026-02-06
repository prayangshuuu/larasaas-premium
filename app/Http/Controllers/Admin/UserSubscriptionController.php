<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class UserSubscriptionController extends Controller
{
    /**
     * Store a new subscription for a user (Manual/Admin override).
     */
    public function store(Request $request, User $user)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        // Deactivate existing active subscriptions to avoid duplicates
        $user->subscriptions()
            ->whereIn('status', ['active', 'past_due'])
            ->update(['status' => 'canceled']); // or deleted, but canceled is better for history
        
        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'stripe_subscription_id' => 'manually_assigned_' . uniqid(),
            'status' => 'active',
            'current_period_end' => now()->addDays(30), // Default 30 days
        ]);

        return back()->with('success', 'Subscription assigned successfully.');
    }

    /**
     * Update an existing subscription (Change Plan).
     */
    public function update(Request $request, User $user, Subscription $subscription)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        // Verify subscription belongs to user
        if ($subscription->user_id !== $user->id) {
            abort(403);
        }

        $plan = Plan::findOrFail($request->plan_id);

        // Update local record
        $subscription->update([
            'plan_id' => $plan->id,
            // We keep current_period_end as is, acting as an immediate swap (or could adjust logic if needed)
            // For now, simple plan ID swap.
        ]);

        // TODO: Handle Stripe update if it's a real Stripe subscription
        // For this task, we focus on the database/admin side per request "Manage + Assign"
        
        return back()->with('success', 'Subscription updated successfully.');
    }

    /**
     * Remove a subscription.
     */
    public function destroy(User $user, Subscription $subscription)
    {
        // Ensure subscription belongs to user
        if ($subscription->user_id !== $user->id) {
            abort(403);
        }

        // Check if there is a stripe_subscription_id to cancel
        if ($subscription->stripe_subscription_id && !str_starts_with($subscription->stripe_subscription_id, 'manually_assigned_')) {
            try {
                $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
                $stripe->subscriptions->cancel($subscription->stripe_subscription_id);
            } catch (\Exception $e) {
                // Log error but proceed with local deletion or return error?
                // For admin force delete, we often want to proceed even if Stripe fails (e.g. already deleted there)
                // But let's flash a warning if it fails.
                session()->flash('warning', 'Could not cancel in Stripe (might already be canceled): ' . $e->getMessage());
            }
        }

        $subscription->delete();

        return back()->with('success', 'Subscription removed successfully.');
    }
}
