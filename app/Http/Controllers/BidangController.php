<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\Event;
use Illuminate\Http\Request;

class BidangController extends Controller
{
    public function index()
    {
        $events = Event::with(['subEvents.bidangs'])
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

        // Cek duplikat
        $exists = Bidang::query()
            ->where('sub_event_id', $request->sub_event_id)
            ->whereRaw('LOWER(nama) = ?', [strtolower($request->nama)])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Bidang dengan nama yang sama sudah ada pada Sub Event ini.',
            ]);
        }

        $bidang = Bidang::create($request->only('sub_event_id', 'nama', 'status'));

        return response()->json([
            'success' => true,
            'bidang'  => array_merge($bidang->toArray(), [
                'update_url'  => route('bidang.update', $bidang->id),
                'destroy_url' => route('bidang.destroy', $bidang->id),
            ]),
        ]);
    }

    public function edit(Bidang $bidang)
    {
        return response()->json($bidang);
    }

    public function update(Request $request, int $id)
{
    $bidang = Bidang::findOrFail($id);

    // Validasi — return JSON jika gagal (karena request AJAX)
    $validated = $request->validate([
        'nama'   => 'required|string|max:255',
        'status' => 'required|in:aktif,tidak_aktif',
    ]);

    // Cek duplikat
    $exists = Bidang::query()
        ->where('sub_event_id', $bidang->sub_event_id)
        ->whereRaw('LOWER(nama) = ?', [strtolower($request->nama)])
        ->where('id', '!=', $bidang->id)
        ->exists();

    if ($exists) {
        return response()->json([
            'success' => false,
            'message' => 'Nama bidang sudah digunakan di sub event ini.',
        ]);
    }

    $bidang->update([
        'nama'   => $request->nama,
        'status' => $request->status,
    ]);

    return response()->json(['success' => true]);
}

    public function destroy(int $id)
    {
        Bidang::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}