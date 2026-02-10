<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = \App\Models\Transaction::with('user')
            ->latest('paid_at')
            ->paginate(20);

        $totalRevenue = \App\Models\Transaction::paid()->sum('amount');
        $monthlyRevenue = \App\Models\Transaction::paid()
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        return view('admin.revenue.index', compact('transactions', 'totalRevenue', 'monthlyRevenue'));
    }

    /**
     * Export transactions as CSV.
     */
    public function export()
    {
        $transactions = \App\Models\Transaction::with('user')->latest('paid_at')->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=transactions_export_" . date('Y-m-d_H-i-s') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'User', 'Email', 'Description', 'Amount', 'Currency', 'Status', 'Date', 'Invoice ID', 'PDF URL']);

            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->id,
                    $transaction->user ? $transaction->user->name : 'N/A',
                    $transaction->user ? $transaction->user->email : 'N/A',
                    $transaction->description ?? 'Subscription Payment',
                    $transaction->amount,
                    $transaction->currency,
                    $transaction->status,
                    $transaction->paid_at ? $transaction->paid_at->format('Y-m-d H:i:s') : 'N/A',
                    $transaction->invoice_id,
                    $transaction->invoice_pdf_url ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // For now, we don't have a detailed view, so just redirect back or show a simple dump if needed.
        // In a real app, this would show the invoice details.
        $transaction = \App\Models\Transaction::findOrFail($id);
        return dd($transaction);
    }
}
