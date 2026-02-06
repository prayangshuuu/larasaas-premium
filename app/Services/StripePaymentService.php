<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\SystemSetting;
use App\Models\User;
use Exception;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Log;

class StripePaymentService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        // Optional: Check if Stripe is enabled in settings before initializing
        // detailed check can be done in methods if we want to allow read-only ops
        $secret = config('services.stripe.secret');
        if (!$secret) {
            // We might not want to throw immediately on construct if we use this service for other things, 
            // but for now it's safer to ensure config exists.
            // Log::warning('Stripe secret key is not configured.');
        }
        
        $this->stripe = new StripeClient($secret ?? 'sk_test_placeholder');
    }

    /**
     * Create or Retrieve Stripe Customer for a User.
     */
    public function getCustomer(User $user)
    {
         if ($user->stripe_id) {
            try {
                return $this->stripe->customers->retrieve($user->stripe_id);
            } catch (\Exception $e) {
                // If deleted in Stripe but exists in DB, ignore and create new
                Log::warning("Stripe customer {$user->stripe_id} not found, creating new one.");
            }
        }

        $customer = $this->stripe->customers->create([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);

        $user->stripe_id = $customer->id;
        $user->save();

        return $customer;
    }

    /**
     * Create a Checkout Session for a Plan subscription.
     *
     * @param User $user
     * @param Plan $plan
     * @return \Stripe\Checkout\Session
     * @throws Exception
     */
    public function createCheckoutSession(User $user, Plan $plan)
    {
        $customer = $this->getCustomer($user);

        // 1. Ensure Plan has a Stripe Price ID
        if (!$plan->stripe_price_id) {
            $this->createStripePriceForPlan($plan);
        }

        // 2. Create Session
        $session = $this->stripe->checkout->sessions->create([
            'customer' => $customer->id,
            'success_url' => url('/billing/success?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url' => url('/billing/cancel'),
            'payment_method_types' => ['card'],
            'mode' => 'subscription',
            'line_items' => [[
                'price' => $plan->stripe_price_id,
                'quantity' => 1,
            ]],
            'metadata' => [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ],
            'subscription_data' => [
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                ]
            ]
        ]);

        return $session;
    }

    /**
     * Cancel the user's active subscription at period end.
     *
     * @param User $user
     * @return void
     * @throws Exception
     */
    public function cancelSubscription(User $user)
    {
        // Find active subscription from DB
        $subscription = $user->subscriptions()->where('status', 'active')->first();

        if (!$subscription || !$subscription->stripe_subscription_id) {
            throw new Exception("No active subscription found to cancel.");
        }

        // Call Stripe
        $this->stripe->subscriptions->update(
            $subscription->stripe_subscription_id,
            ['cancel_at_period_end' => true]
        );
    }

    /**
     * Helper: Create Product and Price in Stripe for a Plan
     *
     * @param Plan $plan
     * @return void
     */
    public function createStripePriceForPlan(Plan $plan)
    {
        // Create Product
        $product = $this->stripe->products->create([
            'name' => $plan->name,
            'metadata' => [
                'plan_id' => $plan->id,
            ],
        ]);

        // Create Price
        $price = $this->stripe->prices->create([
            'unit_amount' => (int)($plan->price * 100), // cents
            'currency' => strtolower($plan->currency),
            'recurring' => ['interval' => $plan->interval],
            'product' => $product->id,
        ]);

        // Update Plan
        $plan->update([
            'stripe_price_id' => $price->id,
        ]);
    }
}
