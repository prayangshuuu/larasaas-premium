<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;

class AnnouncementBanner extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $announcement = null;

        if (Auth::check()) {
            $announcement = Announcement::published()
                ->whereDoesntHave('users', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->latest('published_at')
                ->first();
        }

        return view('components.announcement-banner', compact('announcement'));
    }
}
