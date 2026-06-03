<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\SubEvent;
use Illuminate\Http\Request;

class BidangController extends Controller
{
    public function index()
    {
        $events = \App\Models\Event::with(['subEvents.bidangs'])
            ->whereHas('subEvents')
            ->orderBy('nama_event')
            ->get();

        return view('master.bidang', compact('events'));
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

    public function update(Request $request, $id)
{
    $bidang = Bidang::findOrFail($id);

    $request->validate([
        'nama'         => 'required|string|max:255',
        'sub_event_id' => 'required|exists:sub_events,id',
        'status'       => 'required|in:aktif,tidak_aktif',
    ]);

    $exists = Bidang::where('sub_event_id', $request->sub_event_id)
        ->whereRaw('LOWER(nama) = ?', [strtolower($request->nama)])
        ->where('id', '!=', $bidang->id)
        ->exists();

    if ($exists) {
        return redirect()->back()
            ->withErrors(['nama' => 'Nama bidang sudah digunakan di sub event ini.'])
            ->withInput();
    }

    $bidang->update([
        'nama'         => $request->nama,
        'sub_event_id' => $request->sub_event_id,
        'status'       => $request->status,
    ]);

    return redirect()->back()->with('success', 'Bidang berhasil diperbarui!');
}

    public function destroy(Bidang $bidang)
    {
        Bidang::destroy($bidang->id);

        return redirect()->route('bidang.index')->with('success', 'Bidang berhasil dihapus.');
    }
}