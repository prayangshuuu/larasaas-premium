<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    /**
     * Display a listing of the plans.
     */
    public function index()
    {
        return response()->json(Plan::all());
    }

    /**
     * Store a newly created plan in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'interval' => 'required|in:month,year',
            'features' => 'nullable|array',
            'stripe_price_id' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $plan = Plan::create($validated);

        return response()->json([
            'message' => 'Plan created successfully.',
            'data' => $plan,
        ], 201);
    }

    /**
     * Display the specified plan.
     */
    public function show(Plan $plan)
    {
        return response()->json($plan);
    }

    /**
     * Update the specified plan in storage.
     */
    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'currency' => 'sometimes|string|size:3',
            'interval' => 'sometimes|in:month,year',
            'features' => 'nullable|array',
            'stripe_price_id' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $plan->update($validated);

        return response()->json([
            'message' => 'Plan updated successfully.',
            'data' => $plan,
        ]);
    }

    /**
     * Remove the specified plan from storage (Soft Delete).
     */
    public function destroy(Plan $plan)
    {
        $plan->delete();

        return response()->json([
            'message' => 'Plan deleted successfully.',
        ]);
    }
}
