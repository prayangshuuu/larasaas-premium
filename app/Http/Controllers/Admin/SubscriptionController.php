<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subscriptions = Subscription::with(['user', 'plan'])
            ->latest()
            ->paginate(10);
            
        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $plans = Plan::where('is_active', true)->get();
        return view('admin.subscriptions.create', compact('plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'plan_id' => 'required|exists:plans,id',
            'expires_at' => 'nullable|date|after:today', // Optional manual override
        ]);

        $user = User::where('email', $validated['email'])->firstOrFail();
        $plan = Plan::findOrFail($validated['plan_id']);

        // Cancel existing active subscriptions
        $user->subscriptions()
            ->whereIn('status', ['active', 'past_due', 'trialing'])
            ->update(['status' => 'canceled']);

        // Create new subscription locally
        // Note: This is an internal manual assignment, so we emulate a Stripe sub ID
        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'stripe_subscription_id' => 'manually_assigned_' . uniqid(),
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => $validated['expires_at'] ?? now()->addDays(30),
        ]);

        return redirect()->route('admin.subscriptions.index')->with('success', "Subscription assigned to {$user->name} successfully.");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subscription $subscription)
    {
        $plans = Plan::where('is_active', true)->get();
        return view('admin.subscriptions.edit', compact('subscription', 'plans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'status' => 'required|in:active,canceled,past_due,trialing,expired',
            'current_period_end' => 'required|date',
        ]);

        // Update local record
        $subscription->update([
            'plan_id' => $validated['plan_id'],
            'status' => $validated['status'],
            'current_period_end' => $validated['current_period_end'],
        ]);

        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscription $subscription)
    {
        // If it's a real stripe sub, try to cancel it
        if ($subscription->stripe_subscription_id && !str_starts_with($subscription->stripe_subscription_id, 'manually_assigned_')) {
            try {
                $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
                $stripe->subscriptions->cancel($subscription->stripe_subscription_id);
            } catch (\Exception $e) {
                // proceed anyway
            }
        }

        $subscription->update(['status' => 'canceled']);
        // Optionally delete: $subscription->delete(); 
        // But keeping it as canceled is usually better for records.
        // However, standard resource destroy usually implies delete. 
        // Let's delete it to match "Delete" action expectation, or SoftDelete if model has it.
        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription deleted/cancelled successfully.');
    }
}
