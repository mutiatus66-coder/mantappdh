<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\SubEvent;
use Illuminate\Http\Request;

class BidangController extends Controller
{
    public function index()
    {
        $bidangs = Bidang::with(['subEvent.event'])
            ->orderBy('sub_event_id')
            ->orderBy('nama')
            ->get();

        $subEvents = SubEvent::with('event')
            ->orderByDesc('tahun')
            ->orderBy('sub_event')
            ->get();

        return view('master.bidang', compact('bidangs', 'subEvents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sub_event_id' => 'required|exists:sub_events,id',
            'nama'         => 'required|string|max:255',
            'status'       => 'required|in:aktif,tidak_aktif',
        ]);

        $exists = Bidang::where('sub_event_id', $request->sub_event_id)
            ->whereRaw('LOWER(nama) = ?', [strtolower($request->nama)])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Bidang dengan nama yang sama sudah ada pada Sub Event ini.');
        }

        Bidang::create($request->only('sub_event_id', 'nama', 'status'));

        return redirect()->route('bidang.index')->with('success', 'Bidang berhasil ditambahkan.');
    }

    public function edit(Bidang $bidang)
    {
        return response()->json($bidang);
    }

    public function update(Request $request, Bidang $bidang)
    {
        $request->validate([
            'sub_event_id' => 'required|exists:sub_events,id',
            'nama'         => 'required|string|max:255',
            'status'       => 'required|in:aktif,tidak_aktif',
        ]);

        $exists = Bidang::where('sub_event_id', $request->sub_event_id)
            ->whereRaw('LOWER(nama) = ?', [strtolower($request->nama)])
            ->where('id', '!=', $bidang->id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Bidang dengan nama yang sama sudah ada pada Sub Event ini.');
        }

        $bidang->update($request->only('sub_event_id', 'nama', 'status'));

        return redirect()->route('bidang.index')->with('success', 'Bidang berhasil diperbarui.');
    }

    public function destroy(Bidang $bidang)
    {
    Bidang::destroy($bidang->id);

        return redirect()->route('bidang.index')->with('success', 'Bidang berhasil dihapus.');
    }

    public function bySubEvent(int $subEventId)
    {
        $bidangs = Bidang::where('sub_event_id', $subEventId)
            ->orderBy('nama')
            ->get(['id', 'nama', 'status']);

        return response()->json($bidangs);
    }
}