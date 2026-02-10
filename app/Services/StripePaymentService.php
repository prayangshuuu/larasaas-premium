<?php

namespace App\Services;

use App\Models\User;
use App\Models\Plan;
use App\Models\Coupon;
use App\Models\Subscription;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Carbon\Carbon;

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
     *
     * @param User $user
     * @param Plan $plan
     * @param Coupon|null $coupon Optional coupon to apply
     */
    public function createCheckoutSession(User $user, Plan $plan, ?Coupon $coupon = null)
    {
        $this->createCustomer($user);

        // Calculate discount
        $subtotal = $plan->price;
        $discountAmount = 0;
        $couponId = null;

        if ($coupon) {
            $discountAmount = $coupon->type === 'percent'
                ? round(($subtotal * $coupon->value) / 100, 2)
                : round(min($coupon->value, $subtotal), 2);
            $couponId = $coupon->id;
        }

        $finalAmount = max(0, round($subtotal - $discountAmount, 2));

        if ($this->isMock) {
            // Immediately create subscription and invoice in DB for testing
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'stripe_subscription_id' => 'sub_mock_' . uniqid(),
                'status' => 'active',
                'current_period_end' => Carbon::now()->addMonth(),
            ]);

            Invoice::create([
                'user_id' => $user->id,
                'coupon_id' => $couponId,
                'stripe_invoice_id' => 'in_mock_' . uniqid(),
                'amount' => $finalAmount,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'status' => 'paid',
                'invoice_pdf_url' => '#', 
                'paid_at' => Carbon::now(),
            ]);

            // Return mock session
            return (object) [
                'id' => 'cs_mock_' . uniqid(),
                'url' => route('billing.index', ['checkout' => 'success']),
            ];
        }

        $sessionData = [
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
            'allow_promotion_codes' => $coupon ? false : true,
        ];

        // Apply Stripe promotion code if coupon has one
        if ($coupon && $coupon->stripe_promotion_code_id) {
            $sessionData['discounts'] = [[
                'promotion_code' => $coupon->stripe_promotion_code_id,
            ]];
        }

        return $this->stripe->checkout->sessions->create($sessionData);
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

    /**
     * Create a Coupon and Promotion Code in Stripe.
     *
     * @param array $data
     * @return array ['coupon_id' => string, 'promo_code_id' => string]
     */
    public function createStripeCoupon(array $data)
    {
        if ($this->isMock) {
            return [
                'coupon_id' => 'coupon_mock_' . uniqid(),
                'promo_code_id' => 'promo_mock_' . uniqid(),
            ];
        }

        // 1. Create Coupon
        $couponData = [
            'name' => $data['code'], // Use code as name for easy ID
            'duration' => $data['duration'] ?? 'once', // once, repeating, forever
        ];

        if ($data['type'] === 'percent') {
            $couponData['percent_off'] = $data['value'];
        } else {
            $couponData['amount_off'] = $data['value'] * 100; // Cents
            $couponData['currency'] = 'usd'; // Defaulting to USD for now, strictly speaking should match plan currency
        }

        if (isset($data['duration_in_months']) && $data['duration'] === 'repeating') {
            $couponData['duration_in_months'] = $data['duration_in_months'];
        }

        $coupon = $this->stripe->coupons->create($couponData);

        // 2. Create Promotion Code (The actual code user types)
        $promoData = [
            'coupon' => $coupon->id,
            'code' => $data['code'],
            'active' => true,
        ];

        if (!empty($data['expires_at'])) {
            $promoData['expires_at'] = \Carbon\Carbon::parse($data['expires_at'])->timestamp;
        }

        if (!empty($data['max_uses'])) {
            $promoData['max_redemptions'] = $data['max_uses'];
        }

        $promoCode = $this->stripe->promotionCodes->create($promoData);

        return [
            'coupon_id' => $coupon->id,
            'promo_code_id' => $promoCode->id,
        ];
    }

    /**
     * Update a Stripe Promotion Code (e.g. toggle active status).
     *
     * @param string $promoCodeId  The Stripe Promotion Code ID (e.g. promo_xxx)
     * @param array  $params       Parameters to update (e.g. ['active' => false])
     * @return \Stripe\PromotionCode|null
     */
    public function updateStripePromoCode(string $promoCodeId, array $params)
    {
        if ($this->isMock) {
            return null;
        }

        try {
            return $this->stripe->promotionCodes->update($promoCodeId, $params);
        } catch (\Exception $e) {
            Log::error("Failed to update Stripe promo code: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete (Archive) a Stripe Coupon/Promo Code.
     */
    public function deleteStripeCoupon(string $items) 
    {
        // We really just need to archive the coupon or the promo code. 
        // Archiving the promo code stops it from being used.
        // $items can be the promo code ID.
        
        if ($this->isMock) {
            return true;
        }

        try {
            // Update Promotion Code to inactive
            $this->stripe->promotionCodes->update($items, ['active' => false]);
        } catch (\Exception $e) {
            Log::error("Failed to archive Stripe promo code: " . $e->getMessage());
        }
    }
}
