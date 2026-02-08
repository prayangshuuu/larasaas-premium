<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Announcement;
use Illuminate\Validation\Rule;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $announcements = Announcement::latest()->paginate(10);
        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.announcements.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => ['required', Rule::in(['new', 'improvement', 'fix', 'alert'])],
            'published_at' => 'nullable|date',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']) . '-' . uniqid(); 

        // Check if slug exists and regenerate if needed (simple uniqid suffix is safer for now)
        // Or cleaner: Str::slug($request->title) and validate uniqueness.
        // Let's stick to the prompt: "Auto-generate slug from title using Str::slug()".
        // If I use just Str::slug(), duplicates will fail the unique constraint. 
        // I will use Str::slug and let the unique index handle it, or maybe append uniqid if not unique?
        // The prompt says "Auto-generate slug from title". I'll use Str::slug($title). 
        // To be safe validation-wise:
        
        $slug = \Illuminate\Support\Str::slug($validated['title']);
        $count = Announcement::where('slug', 'LIKE', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }
        $validated['slug'] = $slug;

        Announcement::create($validated);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => ['required', Rule::in(['new', 'improvement', 'fix', 'alert'])],
            'published_at' => 'nullable|date',
        ]);

        if ($announcement->title !== $validated['title']) {
            $slug = \Illuminate\Support\Str::slug($validated['title']);
            $count = Announcement::where('slug', 'LIKE', "{$slug}%")->where('id', '!=', $announcement->id)->count();
            if ($count > 0) {
                $slug .= '-' . ($count + 1);
            }
            $validated['slug'] = $slug;
        }

        $announcement->update($validated);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted successfully.');
    }
}
