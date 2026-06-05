<?php

namespace App\Http\Controllers;

use App\Models\Announcement;

class PublicAnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::where('is_active', true)
                                     ->orderBy('publish_date', 'desc')
                                     ->paginate(9);
        return view('public.pengumuman.index', compact('announcements'));
    }

    public function show($slug)
    {
        $announcement = Announcement::where('slug', $slug)
                                    ->where('is_active', true)
                                    ->firstOrFail();
        return view('public.pengumuman.show', compact('announcement'));
    }
}
