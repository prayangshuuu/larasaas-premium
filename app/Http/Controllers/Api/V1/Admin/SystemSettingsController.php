<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SystemSettingsController extends Controller
{
    public function __construct()
    {
        // Sanctum auth + Admin gate for all endpoints
        $this->middleware(['auth:sanctum', 'admin']);
    }

    /**
     * GET /api/v1/admin/settings
     */
    public function index()
    {
        return response()->json(['data' => Setting::allPairs()]);
    }

    /**
     * GET /api/v1/admin/settings/{key}
     */
    public function show(string $key)
    {
        return response()->json(['data' => [$key => Setting::get($key)]]);
    }

    /**
     * PUT /api/v1/admin/settings/{key}
     * Body: { "value": mixed }
     */
    public function update(Request $request, string $key)
    {
        $this->ensureWriteAllowed();

        $val = $request->input('value', null);

        // booleans arrive as true/false in JSON; Setting handles both schemas
        Setting::put($key, $val);

        return response()->json([
            'message' => 'Updated',
            'data'    => [$key => Setting::get($key)],
        ]);
    }

    /**
     * POST /api/v1/admin/settings/logo
     * Accepts one or more of: app_logo, app_logo_light, app_logo_dark
     */
    public function uploadLogo(Request $request)
    {
        $this->ensureWriteAllowed();

        $request->validate([
            'app_logo'       => ['nullable','file','mimes:png,jpg,jpeg,webp,svg','max:4096'],
            'app_logo_light' => ['nullable','file','mimes:png,jpg,jpeg,webp,svg','max:4096'],
            'app_logo_dark'  => ['nullable','file','mimes:png,jpg,jpeg,webp,svg','max:4096'],
        ]);

        $changed = [];

        // Legacy single file → set both & keep legacy key
        if ($request->hasFile('app_logo')) {
            $publicL = $this->storeManaged($request->file('app_logo'), Setting::get('app.logo_light_path'));
            $publicD = $this->storeManaged($request->file('app_logo'), Setting::get('app.logo_dark_path'));
            Setting::put('app.logo_light_path', $publicL);
            Setting::put('app.logo_dark_path',  $publicD);
            Setting::put('app.logo_path',       $publicL);
            $changed['light'] = $publicL;
            $changed['dark']  = $publicD;
        }

        if ($request->hasFile('app_logo_light')) {
            $public = $this->storeManaged($request->file('app_logo_light'), Setting::get('app.logo_light_path'));
            Setting::put('app.logo_light_path', $public);
            if (!Setting::get('app.logo_path')) {
                Setting::put('app.logo_path', $public);
            }
            $changed['light'] = $public;
        }

        if ($request->hasFile('app_logo_dark')) {
            $public = $this->storeManaged($request->file('app_logo_dark'), Setting::get('app.logo_dark_path'));
            Setting::put('app.logo_dark_path', $public);
            if (!Setting::get('app.logo_path') && !Setting::get('app.logo_light_path')) {
                Setting::put('app.logo_path', $public);
            }
            $changed['dark'] = $public;
        }

        if (!$changed) {
            return response()->json(['message' => 'No files uploaded.'], 422);
        }

        return response()->json(['message' => 'Logo(s) updated','data' => $changed]);
    }

    /**
     * Guard: API write actions are read-only while impersonating (Sanctum SPA uses session).
     */
    private function ensureWriteAllowed(): void
    {
        if (request()->session()->has('impersonated_by')) {
            abort(403, 'Read-only while impersonating.');
        }
    }

    /**
     * Store file to /storage/logos on public disk, delete old managed file,
     * and return public path like "storage/logos/abc.png".
     */
    private function storeManaged($file, ?string $oldPublicPath): string
    {
        $path = $file->store('logos', 'public'); // storage/app/public/logos/...
        $public = 'storage/' . ltrim($path, '/');

        if (is_string($oldPublicPath) && str_starts_with($oldPublicPath, 'storage/')) {
            $rel = substr($oldPublicPath, strlen('storage/')); // strip "storage/"
            if ($rel && Storage::disk('public')->exists($rel)) {
                Storage::disk('public')->delete($rel);
            }
        }

        return $public;
    }
}
