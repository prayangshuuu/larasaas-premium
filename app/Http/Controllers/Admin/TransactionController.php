<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Transaction;
use App\Models\Subscription;
use App\Models\Invoice;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::query()->with(['user', 'coupon']);

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $transactions = $query->latest()->paginate(20);

        return view('admin.transactions.index', compact('transactions'));
    }

    public function approve(Transaction $transaction)
    {
        if ($transaction->status === 'paid') {
            return back()->with('error', 'Transaction already paid.');
        }

        // 1. Mark Transaction as Paid
        $transaction->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // 2. Mark Invoice as Paid
        if ($transaction->invoice_id) {
            $invoice = Invoice::find($transaction->invoice_id);
            if ($invoice) {
                $invoice->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'stripe_invoice_id' => 'bkash_inv_' . $transaction->id . '_' . Str::random(6),
                ]);
            }
        }

        // 3. Create Subscription
        // Retrieve plan from metadata
        $planId = $transaction->payment_metadata['plan_id'] ?? null;
        if ($planId) {
            $plan = Plan::find($planId);
            if ($plan) {
                // Determine end date
                $endDate = $plan->interval === 'year' ? now()->addYear() : now()->addMonth();

                Subscription::create([
                    'user_id' => $transaction->user_id,
                    'plan_id' => $plan->id,
                    'stripe_subscription_id' => 'bkash_sub_' . $transaction->id . '_' . Str::random(6),
                    'status' => 'active',
                    'current_period_end' => $endDate,
                ]);
            }
        }

        return back()->with('success', 'Transaction approved and subscription activated.');
    }

    public function reject(Transaction $transaction)
    {
         if ($transaction->status === 'paid') {
            return back()->with('error', 'Cannot reject a paid transaction.');
        }

        $transaction->update(['status' => 'rejected']);
        
        if ($transaction->invoice_id) {
             $invoice = Invoice::find($transaction->invoice_id);
             if ($invoice) {
                 $invoice->update(['status' => 'cancelled']);
             }
        }

        return back()->with('success', 'Transaction rejected.');
    }
}
