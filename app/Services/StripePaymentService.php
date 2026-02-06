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
        $enabled = SystemSetting::where('key', 'stripe_payment_enabled')->value('value');

        // Check against boolean strings/types (value is JSON-cast in model but likely string 'true'/'false' in practice if not encoded)
        // Adjusting for the 'true' string seeding we did.
        if ($enabled !== 'true' && $enabled !== true && $enabled !== 1) {
             throw new Exception('Stripe payments are currently disabled.');
        }

        $secret = config('services.stripe.secret');
        if (!$secret) {
            throw new Exception('Stripe secret key is not configured.');
        }

        $this->stripe = new StripeClient($secret);
    }

    /**
     * Create or Retrieve Stripe Customer for a User.
     */
    public function getListOfCustomer(User $user)
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
     */
    public function createCheckoutSession(User $user, Plan $plan)
    {
        $customer = $this->getListOfCustomer($user);

        // Ensure Price ID exists
        $priceId = $plan->stripe_price_id;

        if (!$priceId) {
            // Dynamic Price Creation
            $price = $this->stripe->prices->create([
                'unit_amount' => (int) ($plan->price * 100), // cents
                'currency' => $plan->currency,
                'recurring' => ['interval' => $plan->interval],
                'product_data' => [
                    'name' => $plan->name,
                ],
            ]);

            $plan->stripe_price_id = $price->id;
            $plan->save();

            $priceId = $price->id;
        }

        $checkout_session = $this->stripe->checkout->sessions->create([
            'customer' => $customer->id,
            'line_items' => [[
                'price' => $priceId,
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => route('dashboard') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('dashboard'),
            'subscription_data' => [
                'metadata' => [
                    'plan_id' => $plan->id,
                    'user_id' => $user->id,
                ],
            ],
        ]);

        return $checkout_session->url;
    }

    /**
     * Cancel a subscription at period end.
     */
    public function cancelSubscription(User $user)
    {
        // Find active subscription from DB
        $subscription = $user->subscriptions()->where('status', 'active')->first();

        if (!$subscription || !$subscription->stripe_subscription_id) {
            throw new Exception("No active subscription found to cancel.");
        }

        $sub = $this->stripe->subscriptions->update(
            $subscription->stripe_subscription_id,
            ['cancel_at_period_end' => true]
        );

        return $sub;
    }
}
