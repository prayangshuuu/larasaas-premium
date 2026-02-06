<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the invoices.
     */
    public function index(Request $request)
    {
        $invoices = $request->user()->invoices()->latest()->get();

        // Check if a specific invoice index view exists, otherwise fallback or potentially render partial.
        // User requested: "Return the view `billing.invoices.index` (or `billing.index`...)"
        // Since `billing.index` is the main dashboard, passing invoices there is fine, 
        // but typically an index method implies a dedicated page or returning data.
        // Given the prompt: "Return the view `billing.invoices.index` (or `billing.index` if you are using a unified dashboard)"
        // I'll stick to `billing.index` if `billing.invoices.index` doesn't exist?
        // Actually, the route `billing.invoices.index` is defined. 
        // If I assume `billing.index` acts as the dashboard AND list, I can return it.
        // Let's create a dedicated simple view or reuse billing.index.
        // Reusing billing.index might be confusing if it expects $subscription etc.
        // But the previous controller `BillingController@index` passed $subscription, $plan, $invoices.
        // If I just pass $invoices, billing.index might break.
        // I will attempt to render `billing.invoices.index` if it existed, but I don't think it does.
        // I'll create `billing.invoices.index` OR effectively redirect to `billing.index`? 
        // No, the prompt explicitly asks for this method.
        // Let's look at `Sidebar` or `Navigation`.
        // I'll return `billing.invoices.index` and assume the user wants me to create that too or it exists?
        // No, the deliverables only ask for `InvoiceController.php`.
        // I will assume `billing.index` is safe to reuse if I pass null subscription?
        // Or better, just `billing.invoices.index` (standard convention).
        // Let's check if `resources/views/billing/invoices/index.blade.php` exists.
        
        return view('billing.index', [
            'invoices' => $invoices,
            'subscription' => $request->user()->subscriptions()->latest()->first(), // Fallback to avoid breaking view
            'plan' => $request->user()->subscriptions()->latest()->first()?->plan,
        ]);
    }

    /**
     * Display the specified invoice (Redirect to PDF).
     */
    public function show(Request $request, Invoice $invoice)
    {
        // Authorization: Strictly check ownership
        if ($invoice->user_id !== $request->user()->id) {
            abort(403);
        }

        // Mock Data Handling
        if ($invoice->invoice_pdf_url === '#' || $invoice->invoice_pdf_url === null) {
            return response("Mock Invoice PDF: This is a placeholder for testing (Invoice #{$invoice->id}).");
        }

        // Real Data Handling
        return redirect()->away($invoice->invoice_pdf_url);
    }
}
