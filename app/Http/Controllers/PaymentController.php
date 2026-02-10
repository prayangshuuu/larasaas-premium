<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Coupon;
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
        $bkashEnabled = (bool) SystemSetting::where('key', 'bkash_enabled')->value('value');
        $stripeEnabled = (bool) SystemSetting::where('key', 'stripe_enabled')->value('value');
        $bkashNumber = SystemSetting::where('key', 'bkash_admin_number')->value('value');
        $bkashInstruction = SystemSetting::where('key', 'bkash_instruction')->value('value');
        $stripeLogo = SystemSetting::where('key', 'stripe_logo')->value('value');
        $bkashLogo = SystemSetting::where('key', 'bkash_logo')->value('value');
        $couponEnabled = (bool) SystemSetting::where('key', 'coupon_enabled')->value('value');

        return view('billing.checkout', compact(
            'plan', 'bkashEnabled', 'stripeEnabled',
            'bkashNumber', 'bkashInstruction', 'stripeLogo', 'bkashLogo',
            'couponEnabled'
        ));
    }

    /**
     * Validate a coupon code via AJAX.
     */
    public function checkCoupon(Request $request, Plan $plan)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50',
        ]);

        $coupon = Coupon::where('code', strtoupper(trim($request->coupon_code)))
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return response()->json(['valid' => false, 'message' => 'Invalid coupon code.'], 422);
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return response()->json(['valid' => false, 'message' => 'This coupon has expired.'], 422);
        }

        if ($coupon->max_uses && $coupon->times_used >= $coupon->max_uses) {
            return response()->json(['valid' => false, 'message' => 'This coupon has reached its usage limit.'], 422);
        }

        $discountAmount = $this->calculateDiscount($coupon, $plan->price);
        $newTotal = max(0, round($plan->price - $discountAmount, 2));

        return response()->json([
            'valid' => true,
            'discount_amount' => $discountAmount,
            'new_total' => $newTotal,
            'coupon_code' => $coupon->code,
            'message' => "Coupon applied! You save {$plan->currency} {$discountAmount}.",
        ]);
    }

    public function payWithBkash(Request $request, Plan $plan)
    {
        $request->validate([
            'sender_number' => 'required|string',
            'transaction_id' => 'required|string',
            'payment_date' => 'required|date',
            'coupon_code' => 'nullable|string|max:50',
        ]);

        // Calculate pricing with optional coupon
        $subtotal = $plan->price;
        $discountAmount = 0;
        $couponId = null;
        $coupon = null;

        if ($request->filled('coupon_code')) {
            $coupon = $this->validateCoupon($request->coupon_code, $plan->price);
            if ($coupon) {
                $discountAmount = $this->calculateDiscount($coupon, $subtotal);
                $couponId = $coupon->id;
            }
        }

        $finalAmount = max(0, round($subtotal - $discountAmount, 2));

        DB::transaction(function () use ($request, $plan, $subtotal, $discountAmount, $finalAmount, $couponId, $coupon) {
            // Create Invoice
            $invoice = new Invoice();
            $invoice->user_id = $request->user()->id;
            $invoice->amount = $finalAmount;
            $invoice->subtotal = $subtotal;
            $invoice->discount_amount = $discountAmount;
            $invoice->coupon_id = $couponId;
            $invoice->status = 'pending';
            $invoice->save();

            // Create Transaction
            Transaction::create([
                'user_id' => $request->user()->id,
                'invoice_id' => $invoice->id,
                'coupon_id' => $couponId,
                'description' => "Payment for {$plan->name}",
                'amount' => $finalAmount,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'currency' => 'USD',
                'status' => 'pending',
                'payment_method' => 'bkash_manual',
                'sender_number' => $request->input('sender_number'),
                'transaction_id' => $request->input('transaction_id'),
                'payment_date' => $request->input('payment_date'),
                'payment_metadata' => ['plan_id' => $plan->id],
            ]);

            // Increment coupon usage
            if ($coupon) {
                $coupon->increment('times_used');
            }
        });

        return redirect()->route('billing.index')->with('success', 'Payment submitted for verification.');
    }

    public function payWithStripe(Request $request, Plan $plan)
    {
        $user = $request->user();
        $coupon = null;

        if ($request->filled('coupon_code')) {
            $coupon = $this->validateCoupon($request->coupon_code, $plan->price);
        }

        try {
            $session = $this->stripeService->createCheckoutSession($user, $plan, $coupon);

            // Increment coupon usage on redirect to Stripe
            if ($coupon) {
                $coupon->increment('times_used');
            }

            return redirect()->away($session->url);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Unable to initiate checkout: ' . $e->getMessage());
        }
    }

    /**
     * Validate a coupon and return the model if valid, null otherwise.
     */
    private function validateCoupon(string $code, float $planPrice): ?Coupon
    {
        $coupon = Coupon::where('code', strtoupper(trim($code)))
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return null;
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return null;
        }

        if ($coupon->max_uses && $coupon->times_used >= $coupon->max_uses) {
            return null;
        }

        return $coupon;
    }

    /**
     * Calculate the discount amount for a coupon against a price.
     */
    private function calculateDiscount(Coupon $coupon, float $price): float
    {
        if ($coupon->type === 'percent') {
            return round(($price * $coupon->value) / 100, 2);
        }

        // Fixed discount — cannot exceed the price
        return round(min($coupon->value, $price), 2);
    }
}
