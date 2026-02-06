<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'total' => $this->total(),
            'currency' => $this->currency(), // Cashier invoice methods
            'status' => $this->status,
            'date' => $this->date()->toIso8601String(),
            'hosted_invoice_url' => $this->hosted_invoice_url,
            'invoice_pdf' => $this->invoice_pdf, 
            'created' => $this->created,
        ];
    }
}
