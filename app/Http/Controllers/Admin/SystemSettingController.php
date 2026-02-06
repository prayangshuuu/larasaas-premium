<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SystemSettingController extends Controller
{
    /**
     * Update a system setting (toggle).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|exists:system_settings,key',
            'value' => 'required', // Value can be boolean or string, logic handles it
        ]);

        $key = $validated['key'];
        $value = $validated['value'];

        // Update DB
        $setting = SystemSetting::where('key', $key)->firstOrFail();
        $setting->update(['value' => $value]);

        // Update Cache
        Cache::forget("system_setting.{$key}");
        Cache::rememberForever("system_setting.{$key}", function () use ($value) {
            return $value;
        });

        return response()->json([
            'message' => 'Setting updated successfully.',
            'data' => $setting->fresh(),
        ]);
    }

    /**
     * Get a system setting value (cached).
     *
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $key)
    {
        $value = Cache::rememberForever("system_setting.{$key}", function () use ($key) {
            return SystemSetting::where('key', $key)->value('value');
        });

        return response()->json([
            'key' => $key,
            'value' => $value,
        ]);
    }
}
