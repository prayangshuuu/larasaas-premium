<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = Plan::latest()->get();
        // Return view if it exists, otherwise just JSON for now or assume view created later/exists
        // Providing standard view return as requested by prompt "Ensure features input is saved as JSON" implies a form.
        return view('admin.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.plans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'interval' => 'required|in:month,year',
            'features' => 'nullable|string', // Expecting JSON string from generic frontend
            'stripe_price_id' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if (!isset($validated['is_active'])) {
            $validated['is_active'] = false;
        }

        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name']);

        // Handle JSON features decode
        if (!empty($validated['features'])) {
            $decoded = json_decode($validated['features'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $validated['features'] = $decoded;
            } else {
                 // Fallback or empty if invalid JSON
                $validated['features'] = [];
            }
        } else {
             $validated['features'] = [];
        }

        Plan::create($validated);

        return redirect()->route('admin.plans.index')->with('success', 'Plan created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'interval' => 'required|in:month,year',
            'features' => 'nullable|string',
            'stripe_price_id' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);
        
        // Handle checkbox boolean (sometimes not sent if unchecked)
        $validated['is_active'] = $request->has('is_active');

        // Only update slug if name changed? Or always? Usually sync.
        $validated['slug'] = Str::slug($validated['name']);

        // Handle JSON features decode
        if (isset($validated['features'])) {
            $decoded = json_decode($validated['features'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $validated['features'] = $decoded;
            } else {
                $validated['features'] = [];
            }
        }

        $plan->update($validated);

        return redirect()->route('admin.plans.index')->with('success', 'Plan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('admin.plans.index')->with('success', 'Plan deleted successfully.');
    }
}
