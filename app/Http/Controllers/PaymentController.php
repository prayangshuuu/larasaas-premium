<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Transaction;
use App\Models\Invoice;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use App\Services\StripePaymentService;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $stripeService;

    public function __construct(StripePaymentService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function show(Plan $plan)
    {
        $bkashEnabled = SystemSetting::where('key', 'bkash_enabled')->value('value') === '1'; // Or true based on storage
        $stripeEnabled = SystemSetting::where('key', 'stripe_enabled')->value('value') !== '0'; // Default to true if not set? Or use consistent false default.
        $bkashNumber = SystemSetting::where('key', 'bkash_admin_number')->value('value');
        $bkashInstruction = SystemSetting::where('key', 'bkash_instruction')->value('value');
        $stripeLogo = SystemSetting::where('key', 'stripe_logo')->value('value');
        $bkashLogo = SystemSetting::where('key', 'bkash_logo')->value('value');

        // If only Stripe is enabled, redirect to Stripe
        if ($stripeEnabled && !$bkashEnabled) {
             return $this->payWithStripe(request(), $plan);
        }

        // If only Bkash is enabled, show Bkash form (or just the selection page with one option pre-selected)
        // For now, let's just show the selection view.

        return view('billing.checkout', compact('plan', 'bkashEnabled', 'stripeEnabled', 'bkashNumber', 'bkashInstruction', 'stripeLogo', 'bkashLogo'));
    }

    public function payWithBkash(Request $request, Plan $plan)
    {
        $request->validate([
            'sender_number' => 'required|string',
            'transaction_id' => 'required|string',
            'payment_date' => 'required|date',
        ]);

        DB::transaction(function () use ($request, $plan) {
            // Create Invoice
            $invoice = new Invoice();
            $invoice->user_id = $request->user()->id;
            $invoice->amount = $plan->price; // Assuming price is on plan
            $invoice->status = 'pending';
            $invoice->save(); // Need to check Invoice model for fillables if using create()

            // Create Transaction
            Transaction::create([
                'user_id' => $request->user()->id,
                'invoice_id' => $invoice->id,
                'description' => "Payment for {$plan->name}",
                'amount' => $plan->price,
                'currency' => 'USD', // Or get from plan/settings
                'status' => 'pending',
                'payment_method' => 'bkash_manual',
                'sender_number' => $request->input('sender_number'),
                'transaction_id' => $request->input('transaction_id'),
                'payment_date' => $request->input('payment_date'),
                'payment_metadata' => ['plan_id' => $plan->id], // Store plan ID for approval logic
            ]);
        });

        return redirect()->route('billing.index')->with('success', 'Payment submitted for verification.');
    }

    public function payWithStripe(Request $request, Plan $plan)
    {
        // Logic from SubscriptionController::checkout
        $user = $request->user();

        try {
            $session = $this->stripeService->createCheckoutSession($user, $plan);
            return redirect()->away($session->url);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Unable to initiate checkout: ' . $e->getMessage());
        }
    }
}
