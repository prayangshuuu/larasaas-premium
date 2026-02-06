<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * List user invoices.
     */
    public function index(Request $request)
    {
        $invoices = $request->user()->invoices();
        // Convert Cashier Invoice objects to Resource
        return InvoiceResource::collection($invoices);
    }

    /**
     * Show a specific invoice.
     */
    public function show(Request $request, string $invoiceId)
    {
        $invoice = $request->user()->findInvoice($invoiceId);

        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found.'], 404);
        }

        return new InvoiceResource($invoice);
    }
}
