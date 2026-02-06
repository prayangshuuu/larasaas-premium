<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * List user's invoices.
     */
    public function index(Request $request)
    {
        $invoices = $request->user()->invoices()->latest()->paginate(10);
        return view('billing.invoices.index', compact('invoices'));
    }

    /**
     * Show/Download a specific invoice.
     */
    public function show(Request $request, Invoice $invoice)
    {
        // Ensure user owns the invoice
        if ($invoice->user_id !== $request->user()->id) {
            abort(403);
        }

        // Redirect to Stripe hosted PDF invoice URL
        if ($invoice->invoice_pdf_url) {
            return redirect()->away($invoice->invoice_pdf_url);
        }

        return redirect()->back()->with('error', 'Invoice PDF not available.');
    }
}
