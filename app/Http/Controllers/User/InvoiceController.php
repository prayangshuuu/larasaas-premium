<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * List user's invoices.
     */
    public function index()
    {
        $invoices = Auth::user()->invoices()->latest()->get();

        // Returning JSON for API deliverable as requested by context of "System Settings" which was API.
        // Or if this is a view controller? The prompt says "Redirect to invoice_pdf_url" for show.
        // Let's assume API/JSON for index, or a View if it were a full app.
        // Given previous deliverables were API routes for admin, but user area usually has views.
        // Prompt says: "Method index to list user's invoices."
        // I will return JSON for consistency with "Deliverable: Provide ... Controller", keeping it agnostic or View if blade exists.
        // But since I don't have blade templates for invoices, JSON is safer.
        return response()->json($invoices);
    }

    /**
     * Redirect to the invoice PDF.
     */
    public function show($invoiceId)
    {
        // Ensure invoice belongs to user
        $invoice = Auth::user()->invoices()->findOrFail($invoiceId);

        if (!$invoice->invoice_pdf_url) {
            abort(404, 'Invoice PDF not found.');
        }

        return redirect()->away($invoice->invoice_pdf_url);
    }
}
