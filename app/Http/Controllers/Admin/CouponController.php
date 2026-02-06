<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Services\StripePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    protected $stripeService;

    public function __construct(StripePaymentService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coupons = Coupon::latest()->paginate(10);
        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.coupons.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code|alpha_num|uppercase',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:1',
            'duration' => 'required|in:once,repeating,forever',
            'duration_in_months' => 'nullable|required_if:duration,repeating|integer|min:1',
            'expires_at' => 'nullable|date|after:today',
            'max_uses' => 'nullable|integer|min:1',
        ]);

        try {
            // Create in Stripe
            $stripeData = $this->stripeService->createStripeCoupon($validated);

            // Create locally
            Coupon::create([
                'code' => $validated['code'],
                'type' => $validated['type'],
                'value' => $validated['value'],
                'stripe_coupon_id' => $stripeData['coupon_id'],
                'stripe_promotion_code_id' => $stripeData['promo_code_id'],
                'expires_at' => $validated['expires_at'],
                'max_uses' => $validated['max_uses'],
                'is_active' => true,
            ]);

            return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');

        } catch (\Exception $e) {
            Log::error('Coupon creation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to create coupon: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        try {
            // Archive in Stripe
            if ($coupon->stripe_promotion_code_id) {
                $this->stripeService->deleteStripeCoupon($coupon->stripe_promotion_code_id);
            }

            // Remove locally
            $coupon->delete();

            return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete coupon: ' . $e->getMessage());
        }
    }
}
