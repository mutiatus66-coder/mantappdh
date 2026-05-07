<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::all();
        return view('master.event', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'jenis'      => 'required|in:INOTEK,INODA',
        ]);

        Event::create($request->only('nama_event', 'jenis'));

        return redirect()->back()->with('success', 'Event berhasil ditambahkan!');
    }
}