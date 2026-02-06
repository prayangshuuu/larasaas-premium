<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Http\Resources\PlanResource;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * List all active subscription plans.
     */
    public function index()
    {
        $plans = Plan::where('is_active', true)->get();
        return PlanResource::collection($plans);
    }
}
