<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PricingController extends Controller
{
    /**
     * Show the pricing page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $plans = \App\Models\Plan::where('is_active', true)
            ->orderBy('price')
            ->get();

        return view('pricing.index', compact('plans'));
    }
}
