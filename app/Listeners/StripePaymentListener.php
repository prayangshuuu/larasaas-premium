<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StripePaymentListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $payload = $event->payload;

        if ($payload['type'] === 'invoice.payment_succeeded' || $payload['type'] === 'charge.succeeded') {
            $data = $payload['data']['object'];
            
            // Find user by stripe_id
            $user = \App\Models\User::where('stripe_id', $data['customer'] ?? null)->first();

            if ($user) {
                // Default description from Stripe
                $description = 'Subscription Payment';
                $stripeDescription = null;
                $priceId = null;

                // Try to get details from line items
                if (isset($data['lines']['data'][0])) {
                    $item = $data['lines']['data'][0];
                    $stripeDescription = $item['description'] ?? null;
                    $priceId = $item['price']['id'] ?? null;
                } elseif (isset($data['description'])) {
                    $stripeDescription = $data['description'];
                }

                // logic to find local Plan
                $plan = null;
                $amount = ($data['amount_paid'] ?? $data['amount']) / 100;
                $currency = $data['currency'];

                // 1. Try by Stripe Price ID
                if ($priceId) {
                    $plan = \App\Models\Plan::where('stripe_price_id', $priceId)->first();
                }

                // 2. Fallback: Try by Amount + Currency (if Price ID didn't match or wasn't set locally)
                if (!$plan) {
                    $plan = \App\Models\Plan::where('price', $amount)
                        ->where('currency', $currency)
                        ->first();
                }

                // Determine final description
                if ($plan) {
                    $description = $plan->name; // e.g. "Se"
                } elseif ($stripeDescription) {
                    $description = $stripeDescription;
                }

                \App\Models\Transaction::updateOrCreate(
                    ['invoice_id' => $data['id']],
                    [
                        'user_id' => $user->id,
                        'description' => $description,
                        'amount' => $amount,
                        'currency' => $currency,
                        'status' => 'paid',
                        'invoice_pdf_url' => $data['invoice_pdf'] ?? $data['hosted_invoice_url'] ?? null,
                        'payment_method' => 'card', // Simplification, could be dynamic
                        'paid_at' => \Carbon\Carbon::createFromTimestamp($data['created']),
                    ]
                );
            }
        }
    }
}
