<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\SubEvent;
use Illuminate\Http\Request;

class SubEventController extends Controller
{
    public function index()
    {
        $subEvents = SubEvent::with('event')
            ->orderByDesc('tahun')
            ->orderBy('sub_event')
            ->get();

        $events = Event::orderBy('nama_event')->get();

        return view('master.sub-event', compact('subEvents', 'events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id'  => 'required|exists:events,id',
            'tahun'     => 'required|digits:4|integer',
            'sub_event' => 'required|string|max:255',
            'kategori'  => 'nullable|string|max:255',
            'mulai'     => 'required|date',
            'berakhir'  => 'required|date|after_or_equal:mulai',
        ]);

        SubEvent::create($request->only('event_id', 'tahun', 'sub_event', 'kategori', 'mulai', 'berakhir'));

        return redirect()->route('sub-event.index')->with('success', 'Sub Event berhasil ditambahkan.');
    }

    public function edit(SubEvent $subEvent)
    {
        return response()->json($subEvent);
    }

    public function update(Request $request, SubEvent $subEvent)
    {
        $request->validate([
            'event_id'  => 'required|exists:events,id',
            'tahun'     => 'required|digits:4|integer',
            'sub_event' => 'required|string|max:255',
            'kategori'  => 'nullable|string|max:255',
            'mulai'     => 'required|date',
            'berakhir'  => 'required|date|after_or_equal:mulai',
        ]);

        $subEvent->update($request->only('event_id', 'tahun', 'sub_event', 'kategori', 'mulai', 'berakhir'));

        return redirect()->route('sub-event.index')->with('success', 'Sub Event berhasil diperbarui.');
    }

    public function destroy(SubEvent $subEvent)
    {

        if ($subEvent->bidangs()->exists()) {
            return redirect()->back()->with('error', 'Sub Event tidak dapat dihapus karena masih memiliki Bidang.');
        }

        $subEvent->delete();

        return redirect()->route('sub-event.index')->with('success', 'Sub Event berhasil dihapus.');
    }
}