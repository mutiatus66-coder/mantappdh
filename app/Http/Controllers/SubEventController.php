<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubEvent;
use App\Models\Event;

class SubEventController extends Controller
{
    public function index()
    {
        $subEvents = SubEvent::orderBy('tahun', 'desc')->orderBy('id', 'desc')->get();
        $events    = Event::orderBy('nama_event')->get();

        return view('master.sub-event', compact('subEvents', 'events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id'  => 'required|exists:events,id',
            'tahun'     => 'required|digits:4',
            'sub_event' => 'required|string|max:255',
            'mulai'     => 'required|date',
            'berakhir'  => 'required|date|after_or_equal:mulai',
        ]);

        SubEvent::create([
            'event_id'  => $request->event_id,
            'tahun'     => (int) $request->tahun,
            'sub_event' => $request->sub_event,
            'kategori'  => $request->kategori ?? 'SEMUA BIDANG',
            'mulai'     => $request->mulai,
            'berakhir'  => $request->berakhir,
        ]);

        return redirect()->route('sub-event.index')
                         ->with('success', 'Sub Event berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $item = SubEvent::findOrFail($id);
        return response()->json($item);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'event_id'  => 'required|exists:events,id',
            'tahun'     => 'required|digits:4',
            'sub_event' => 'required|string|max:255',
            'mulai'     => 'required|date',
            'berakhir'  => 'required|date|after_or_equal:mulai',
        ]);

        $subEvent = SubEvent::findOrFail($id);
        $subEvent->update([
            'event_id'  => $request->event_id,
            'tahun'     => (int) $request->tahun,
            'sub_event' => $request->sub_event,
            'kategori'  => $request->kategori ?? 'SEMUA BIDANG',
            'mulai'     => $request->mulai,
            'berakhir'  => $request->berakhir,
        ]);

        return redirect()->route('sub-event.index')
                         ->with('success', 'Sub Event berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        SubEvent::findOrFail($id)->delete();

        return redirect()->route('sub-event.index')
                         ->with('success', 'Sub Event berhasil dihapus.');
    }
}