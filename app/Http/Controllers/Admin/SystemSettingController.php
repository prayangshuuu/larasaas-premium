<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SystemSettingController extends Controller
{
    /**
     * Update system settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'subscription_module_enabled' => 'nullable|boolean',
            'stripe_payment_enabled' => 'nullable|boolean',
        ]);

        // Helper to update or create
        $updateSetting = function ($key, $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value] 
            );
        };

        // Handle checkbox inputs (if unchecked, they might be missing, so use ->has or default false)
        // But if these are just API/JSON endpoints, boolean is fine. Assuming form submission with checkbox:
        $subEnabled = $request->has('subscription_module_enabled'); // simplified for checkboxes
        $stripeEnabled = $request->has('stripe_payment_enabled');

        $updateSetting('subscription_module_enabled', $subEnabled); // Stored as boolean (JSON cast in model)
        $updateSetting('stripe_payment_enabled', $stripeEnabled);

        // Clear cache
        Cache::forget('system_setting.subscription_module_enabled');
        Cache::forget('system_setting.stripe_payment_enabled'); // Good practice to clear both

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
