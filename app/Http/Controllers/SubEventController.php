<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubEvent;

class SubEventController extends Controller
{
    // Daftar event (bisa dipindah ke tabel events jika sudah ada relasi)
    private array $events = [
        'LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)',
        'INOVASI DAERAH KAB. MAGETAN',
        'PAMERAN INOVASI DAN TEKNOLOGI',
        'KOMPETISI INOVASI DIGITAL',
    ];

    public function index()
    {
        $subEvents = SubEvent::orderBy('tahun', 'desc')->orderBy('id', 'desc')->get();
        $events    = $this->events;

        return view('master.sub-event', compact('subEvents', 'events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event'     => 'required|string',
            'tahun'     => 'required|digits:4',
            'sub_event' => 'required|string|max:255',
            'mulai'     => 'required|date',
            'berakhir'  => 'required|date|after_or_equal:mulai',
        ]);

        SubEvent::create([
            'event_id'  => 1, // sesuaikan jika ada relasi ke tabel events
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
            'event'     => 'required|string',
            'tahun'     => 'required|digits:4',
            'sub_event' => 'required|string|max:255',
            'mulai'     => 'required|date',
            'berakhir'  => 'required|date|after_or_equal:mulai',
        ]);

        $subEvent = SubEvent::findOrFail($id);
        $subEvent->update([
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