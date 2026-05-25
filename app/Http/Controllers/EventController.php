<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
    // ── DATA MASTER — disimpan di session ────────────────────
    public static function getData(): array
    {
        return session('events', [
            ['id' => 1, 'nama_event' => 'LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)', 'jenis' => 'INOTEK'],
            ['id' => 2, 'nama_event' => 'INOVASI DAERAH KAB. MAGETAN',                'jenis' => 'INODA'],
            ['id' => 3, 'nama_event' => 'PAMERAN INOVASI DAN TEKNOLOGI',               'jenis' => 'INOTEK'],
            ['id' => 4, 'nama_event' => 'KOMPETISI INOVASI DIGITAL',                   'jenis' => 'INOTEK'],
        ]);
    }

    private static function saveData(array $data): void
    {
        session(['events' => array_values($data)]);
    }

    // ── CRUD ─────────────────────────────────────────────────
    public function index()
    {
        $events = self::getData();
        return view('master.event', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'jenis'      => 'required|in:INOTEK,INODA',
        ]);

        $data  = self::getData();
        $maxId = count($data) ? max(array_column($data, 'id')) : 0;

        $data[] = [
            'id'         => $maxId + 1,
            'nama_event' => $request->nama_event,
            'jenis'      => $request->jenis,
        ];

        self::saveData($data);
        return redirect()->back()->with('success', 'Event berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'jenis'      => 'required|in:INOTEK,INODA',
        ]);

        $data = self::getData();
        foreach ($data as &$row) {
            if ($row['id'] == $id) {
                $row['nama_event'] = $request->nama_event;
                $row['jenis']      = $request->jenis;
                break;
            }
        }

        self::saveData($data);
        return redirect()->back()->with('success', 'Event berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $data = array_filter(self::getData(), fn($r) => $r['id'] != $id);
        self::saveData($data);
        return redirect()->back()->with('success', 'Event berhasil dihapus.');
    }
}