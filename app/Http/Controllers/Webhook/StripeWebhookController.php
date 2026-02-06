<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionWelcome;
use App\Mail\InvoicePaid;

class StripeWebhookController extends Controller
{
    /**
     * Handle Stripe Webhook.
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe Webhook Signature Verification Failed');
            return response('Invalid signature', 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'invoice.payment_succeeded':
                $this->handleInvoicePaymentSucceeded($event->data->object);
                break;
            case 'invoice.payment_failed':
                $this->handleInvoicePaymentFailed($event->data->object);
                break;
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;
            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;
            default:
                // Unexpected event type
                // Log::info('Received unknown event type ' . $event->type);
        }

        return response('Webhook Handled', 200);
    }

    /**
     * Handle Checkout Session Completed.
     * This is where we strictly create the subscription record.
     */
    /**
     * Handle Checkout Session Completed.
     * This is where we strictly create the subscription record.
     */
    protected function handleCheckoutSessionCompleted($session)
    {
        Log::info('Handling checkout.session.completed', ['session_id' => $session->id]);

        if ($session->mode !== 'subscription') {
            return;
        }

        $userId = $session->metadata->user_id ?? null;
        $planId = $session->metadata->plan_id ?? null;

        if (!$userId || !$planId) {
            Log::error('Missing user_id or plan_id in Checkout Session metadata.');
            return;
        }

        $subscriptionId = $session->subscription;
        
        // Fetch subscription details from Stripe to get period end
        try {
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
            $stripeSubscription = $stripe->subscriptions->retrieve($subscriptionId);

            Subscription::updateOrCreate(
                ['stripe_subscription_id' => $subscriptionId],
                [
                    'user_id' => $userId,
                    'plan_id' => $planId,
                    'status' => 'active', 
                    // Important: Use the period end from Stripe
                    'current_period_end' => Carbon::createFromTimestamp($stripeSubscription->current_period_end),
                ]
            );
            
            Log::info("Subscription created for User {$userId}, Plan {$planId}");

            // Send Welcome Email
            $plan = Plan::find($planId);
            $user = User::find($userId);
            if ($user && $plan) {
                Mail::to($user)->send(new SubscriptionWelcome($plan));
            }

        } catch (\Exception $e) {
             Log::error('Error fetching subscription in checkout session completed: ' . $e->getMessage());
        }
    }

    protected function handleInvoicePaymentSucceeded($invoice)
    {
        Log::info('Handling invoice.payment_succeeded', ['invoice' => $invoice->id]);

        $user = User::where('stripe_id', $invoice->customer)->first();
        if (!$user) {
            Log::error('User not found for Stripe Customer: ' . $invoice->customer);
            return;
        }

        // Upsert Invoice
        Invoice::updateOrCreate(
            ['stripe_invoice_id' => $invoice->id],
            [
                'user_id' => $user->id,
                'amount' => $invoice->amount_paid / 100, // Stripe uses cents
                'status' => 'paid',
                'invoice_pdf_url' => $invoice->hosted_invoice_url ?? $invoice->invoice_pdf, 
                'paid_at' => Carbon::createFromTimestamp($invoice->status_transitions->paid_at ?? time()),
            ]
        );

        // Update Subscription
        if ($invoice->subscription) {
            $subscription = Subscription::where('stripe_subscription_id', $invoice->subscription)->first();

            if ($subscription) {
                 // Update valid status and extend period
                 // We can fetch the subscription object to be 100% sure about the new period end
                 try {
                     $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
                     $stripeSubscription = $stripe->subscriptions->retrieve($invoice->subscription);

                     $subscription->update([
                         'status' => 'active',
                         'current_period_end' => Carbon::createFromTimestamp($stripeSubscription->current_period_end),
                     ]);
                     Log::info("Subscription updated via Invoice for User {$user->id}");
                     
                     // Send Invoice Paid Email
                     Mail::to($user)->send(new InvoicePaid(Invoice::where('stripe_invoice_id', $invoice->id)->first()));
                 } catch (\Exception $e) {
                      Log::error('Error updating subscription in invoice payment succeeded: ' . $e->getMessage());
                 }

            } else {
                 Log::warning("Subscription {$invoice->subscription} not found during invoice payment. It might not be created yet if Checkout hook hasn't fired.");
                 // Should we create it here? 
                 // Ideally Checkout Session handles creation. But redundancy is safe if we had metadata. 
                 // Invoice object has metadata but it might be on the subscription line item.
                 // For now, let's rely on Checkout Session or a retry.
            }
        }
    }

    protected function handleInvoicePaymentFailed($invoice)
    {
        $user = User::where('stripe_id', $invoice->customer)->first();
        if (!$user) return;

        Invoice::updateOrCreate(
            ['stripe_invoice_id' => $invoice->id],
            [
                'user_id' => $user->id,
                'amount' => $invoice->amount_due / 100,
                'status' => 'pending', 
                'invoice_pdf_url' => $invoice->hosted_invoice_url,
                'paid_at' => null,
            ]
        );
    }

    protected function handleSubscriptionDeleted($subscriptionObj)
    {
        $subscription = Subscription::where('stripe_subscription_id', $subscriptionObj->id)->first();
        if ($subscription) {
            $subscription->update(['status' => 'canceled']);
        }
    }
}
