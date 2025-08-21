<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ThemeController extends Controller
{
    /**
     * Update the authenticated user's theme preference.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'theme' => ['required', Rule::in(['nord', 'dim'])],
        ]);

        $user = Auth::user();
        $user->theme = $validated['theme'];
        $user->save();

        return response()->json(['success' => true, 'theme' => $user->theme]);
    }
}
