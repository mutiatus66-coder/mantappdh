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

        Event::create([
            'nama_event' => $request->nama_event,
            'jenis'      => $request->jenis,
        ]);

        return redirect()->back()->with('success', 'Event berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'nama_event' => 'required|string|max:255',
            'jenis'      => 'required|in:INOTEK,INODA',
        ]);

        $event->update([
            'nama_event' => $request->nama_event,
            'jenis'      => $request->jenis,
        ]);

        return redirect()->back()->with('success', 'Event berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Event::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Event berhasil dihapus.');
    }
}