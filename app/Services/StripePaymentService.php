<?php

namespace App\Services;

use App\Models\User;
use App\Models\Plan;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class StripePaymentService
{
    protected $stripe;
    protected $isMock = false;

    public function __construct()
    {
        $secret = config('services.stripe.secret');

        if (!$secret) {
            throw new \RuntimeException('Stripe Secret Key is missing. Please set STRIPE_SECRET in your .env file.');
        }

        // Mock detection
        if (str_contains($secret, 'placeholder')) {
            $this->isMock = true;
            // No need to instantiate real client if mocking
            return;
        }

        $this->stripe = new StripeClient($secret);
    }

    /**
     * Create or retrieve Stripe Customer for User.
     */
    public function createCustomer(User $user)
    {
        if ($this->isMock) {
            if (!$user->stripe_id) {
                // Generate a fake stripe ID
                $user->update(['stripe_id' => 'cus_mock_' . uniqid()]);
            }
            // Return a mock object compatible with Stripe Customer
            return (object) [
                'id' => $user->stripe_id,
                'email' => $user->email,
                'name' => $user->name,
            ];
        }

        if ($user->stripe_id) {
            return $this->stripe->customers->retrieve($user->stripe_id);
        }

        $customer = $this->stripe->customers->create([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);

        $user->update(['stripe_id' => $customer->id]);

        return $customer;
    }

    /**
     * Create a Checkout Session for a subscription.
     */
    public function createCheckoutSession(User $user, Plan $plan)
    {
        $this->createCustomer($user);

        if ($this->isMock) {
            // Return mock session
            return (object) [
                'id' => 'cs_mock_' . uniqid(),
                'url' => route('billing.index', ['checkout' => 'success']),
            ];
        }

        return $this->stripe->checkout->sessions->create([
            'customer' => $user->stripe_id,
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $plan->stripe_price_id,
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => route('billing.index', ['checkout' => 'success']),
            'cancel_url' => route('billing.index', ['checkout' => 'cancel']),
            'metadata' => [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ],
            'subscription_data' => [
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                ],
            ],
        ]);
    }

    /**
     * Cancel a subscription at period end.
     */
    public function cancelSubscription(User $user)
    {
        $subscription = $user->subscriptions()->where('status', 'active')->firstOrFail();

        if ($this->isMock) {
            $subscription->update(['status' => 'canceled']);
            return (object) ['id' => $subscription->stripe_subscription_id, 'status' => 'canceled'];
        }

        $stripeSub = $this->stripe->subscriptions->update(
            $subscription->stripe_subscription_id,
            ['cancel_at_period_end' => true]
        );

        // Local update to reflect pending cancellation (grace period)
        // We might want to keep status 'active' but relying on specific flag or check 'current_period_end'
        // For simplicity, we keep it as is, or mark a local "cancels_at" if column existed. 
        // Standard Stripe logic: It stays active until period end.
        // We can optionally set a local status like 'canceled' if your app logic treats grace period that way, 
        // but usually 'active' + 'ends_at' future date is better. 
        // Given the prompt asks to check `$subscription->status === 'canceled'` for grace period, 
        // I will update the status to 'canceled' BUT ensure logic knows it's grace period if end date is future.
        // ACTUALLY: The prompt view logic implies:
        // "Active" check: @if($subscription->status === 'active')
        // "Generic Canceled/Grace": @if($subscription->status === 'canceled')
        // So I will set it to 'canceled' here to match the requested View logic.
        
        $subscription->update([
            'status' => 'canceled', 
            // 'current_period_end' is already set to the end date
        ]);

        return $stripeSub;
    }

    /**
     * Resume a canceled subscription.
     */
    public function resumeSubscription(User $user)
    {
        // Find latest canceled subscription that hasn't expired yet
        $subscription = $user->subscriptions()
            ->where('status', 'canceled')
            ->where('current_period_end', '>', now())
            ->firstOrFail();

        if ($this->isMock) {
            $subscription->update(['status' => 'active']);
            return (object) ['id' => $subscription->stripe_subscription_id, 'status' => 'active'];
        }

        $stripeSub = $this->stripe->subscriptions->update(
            $subscription->stripe_subscription_id,
            ['cancel_at_period_end' => false]
        );

        $subscription->update(['status' => 'active']);

        return $stripeSub;
    }

    /**
     * Create a Stripe Billing Portal session.
     */
    public function createBillingPortalSession(User $user)
    {
        $this->createCustomer($user);

        if ($this->isMock) {
            return (object) [
                'url' => route('billing.index', ['portal' => 'mock_success']),
            ];
        }

        return $this->stripe->billingPortal->sessions->create([
            'customer' => $user->stripe_id,
            'return_url' => route('billing.index'),
        ]);
    }
}
