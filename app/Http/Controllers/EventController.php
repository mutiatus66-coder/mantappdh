<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
    // Ambil daftar events unik dari data sub_events milik Admin controller
    private function getEvents(): array
    {
        $subEvents = session('sub_events', [
            ['id'=>1,'tahun'=>2022,'event'=>'LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)','sub_event'=>'LOMBA INOTEK 2022','kategori'=>'SEMUA BIDANG','mulai'=>'2022-08-12','berakhir'=>'2022-10-03'],
            ['id'=>2,'tahun'=>2023,'event'=>'LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)','sub_event'=>'LOMBA INOTEK (INOTEK AWARD) 2023','kategori'=>'SEMUA BIDANG','mulai'=>'2023-03-15','berakhir'=>'2023-07-23'],
            ['id'=>3,'tahun'=>2023,'event'=>'INOVASI DAERAH KAB. MAGETAN','sub_event'=>'PELAPORAN INOVASI DAERAH 2023','kategori'=>'SEMUA BIDANG','mulai'=>'2023-09-09','berakhir'=>'2023-12-20'],
            ['id'=>4,'tahun'=>2024,'event'=>'LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)','sub_event'=>'LOMBA INOVASI DAN TEKNOLOGI 2024','kategori'=>'SEMUA','mulai'=>'2024-04-01','berakhir'=>'2025-03-31'],
        ]);

        return session('events', [
            ['id' => 1, 'nama_event' => 'LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)', 'jenis' => 'INOTEK'],
            ['id' => 2, 'nama_event' => 'INOVASI DAERAH KAB. MAGETAN',                'jenis' => 'INODA'],
            ['id' => 3, 'nama_event' => 'KOMPETISI INOVASI DIGITAL',                  'jenis' => 'INOTEK'],
        ]);
    }

    private function saveEvents(array $data): void
    {
        session(['events' => array_values($data)]);
    }

    public function index()
    {
        $events = $this->getEvents();
        return view('master.event', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'jenis'      => 'required|in:INOTEK,INODA',
        ]);

        $data  = $this->getEvents();
        $maxId = count($data) ? max(array_column($data, 'id')) : 0;

        $data[] = [
            'id'         => $maxId + 1,
            'nama_event' => $request->nama_event,
            'jenis'      => $request->jenis,
        ];

        $this->saveEvents($data);
        return redirect()->route('event.index')->with('success', 'Event berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'jenis'      => 'required|in:INOTEK,INODA',
        ]);

        $data = $this->getEvents();
        foreach ($data as &$row) {
            if ($row['id'] === $id) {
                $row['nama_event'] = $request->nama_event;
                $row['jenis']      = $request->jenis;
                break;
            }
        }

        $this->saveEvents($data);
        return redirect()->route('event.index')->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $data = array_filter($this->getEvents(), fn($r) => $r['id'] !== $id);
        $this->saveEvents($data);
        return redirect()->route('event.index')->with('success', 'Event berhasil dihapus.');
    }
}