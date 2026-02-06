<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Http\Resources\PlanResource;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = Plan::all();
        return PlanResource::collection($plans);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:plans,slug',
            'stripe_id' => 'required|string|max:255|unique:plans,stripe_id',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_interval' => 'required|in:month,year',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $plan = Plan::create($validated);
        return new PlanResource($plan);
    }

    /**
     * Display the specified resource.
     */
    public function show(Plan $plan)
    {
        return new PlanResource($plan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:plans,slug,' . $plan->id,
            'stripe_id' => 'sometimes|string|max:255|unique:plans,stripe_id,' . $plan->id,
            'price' => 'sometimes|numeric|min:0',
            'currency' => 'sometimes|string|size:3',
            'billing_interval' => 'sometimes|in:month,year',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $plan->update($validated);
        return new PlanResource($plan);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plan $plan)
    {
        $plan->delete();
        return response()->json(null, 204);
    }
}
