<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\SubEvent;

class SubEventController extends Controller
{
    // ── INDEX ─────────────────────────────────────────────────
    public function index()
    {
        $subEvents = SubEvent::with('event')->orderBy('tahun', 'desc')->get();
        $events    = Event::all();
        return view('master.sub-event', compact('subEvents', 'events'));
    }

    // ── STORE ─────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'event_id'  => 'required|integer|exists:events,id',
            'tahun'     => 'required|digits:4',
            'sub_event' => 'required|string|max:255',
            'mulai'     => 'required|date',
            'berakhir'  => 'required|date|after_or_equal:mulai',
        ]);

        SubEvent::create([
            'event_id'  => $request->event_id,
            'tahun'     => $request->tahun,
            'sub_event' => $request->sub_event,
            'kategori'  => $request->kategori ?? 'SEMUA BIDANG',
            'mulai'     => $request->mulai,
            'berakhir'  => $request->berakhir,
        ]);

        return redirect()->route('sub-event.index')->with('success', 'Sub Event berhasil ditambahkan.');
    }

    // ── EDIT (JSON untuk modal) ───────────────────────────────
    public function edit(int $id)
    {
        $item = SubEvent::findOrFail($id);
        return response()->json($item);
    }

    // ── UPDATE ────────────────────────────────────────────────
    public function update(Request $request, int $id)
    {
        $request->validate([
            'event_id'  => 'required|integer|exists:events,id',
            'tahun'     => 'required|digits:4',
            'sub_event' => 'required|string|max:255',
            'mulai'     => 'required|date',
            'berakhir'  => 'required|date|after_or_equal:mulai',
        ]);

        SubEvent::findOrFail($id)->update([
            'event_id'  => $request->event_id,
            'tahun'     => $request->tahun,
            'sub_event' => $request->sub_event,
            'kategori'  => $request->kategori ?? 'SEMUA BIDANG',
            'mulai'     => $request->mulai,
            'berakhir'  => $request->berakhir,
        ]);

        return redirect()->route('sub-event.index')->with('success', 'Sub Event berhasil diperbarui.');
    }

    // ── DESTROY ───────────────────────────────────────────────
    public function destroy(int $id)
    {
        SubEvent::findOrFail($id)->delete();
        return redirect()->route('sub-event.index')->with('success', 'Sub Event berhasil dihapus.');
    }
}