<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('jenis')->orderBy('nama_event')->get();
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

    public function edit(Event $event)
    {
        return response()->json($event);
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'jenis'      => 'required|in:INOTEK,INODA',
        ]);

        $event->update($request->only('nama_event', 'jenis'));

        return redirect()->back()->with('success', 'Event berhasil diperbarui!');
    }

    public function destroy(Event $event)
    {
        if ($event->subEvents()->exists()) {
            return redirect()->back()->with('error', 'Event tidak dapat dihapus karena masih memiliki Sub Event.');
        }

        Event::destroy($event->id);

        return redirect()->back()->with('success', 'Event berhasil dihapus.');
    }
}