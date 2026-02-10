<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application landing page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $featuredPlans = \App\Models\Plan::where('is_active', true)
            ->where('is_featured', true)
            ->get();

        return view('welcome', compact('featuredPlans'));
    }
}
