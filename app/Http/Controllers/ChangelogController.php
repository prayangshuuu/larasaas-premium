<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Announcement;

class ChangelogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $announcements = Announcement::published()->paginate(10);
        return view('changelog.index', compact('announcements'));
    }

    /**
     * Mark the announcement as read.
     */
    public function markAsRead(Request $request, Announcement $announcement)
    {
        $announcement->markAsRead($request->user());

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('success', 'Marked as read.');
    }
}
