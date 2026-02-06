<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SubscriptionSettingsController extends Controller
{
    /**
     * Update subscription settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'subscription_module_enabled' => 'required|boolean',
        ]);

        foreach ($validated as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => 'subscription']
            );
        }

        return response()->json(['message' => 'Subscription settings updated successfully.']);
    }
}
