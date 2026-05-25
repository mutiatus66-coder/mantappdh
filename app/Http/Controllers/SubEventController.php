<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubEventController extends Controller
{
    // ── DATA MASTER ───────────────────────────────────────────
    public static function getData(): array
    {
        return session('sub_events', [
            ['id'=>1,'tahun'=>2022,'event_id'=>1,'event'=>'LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)','sub_event'=>'LOMBA INOTEK 2022','kategori'=>'SEMUA BIDANG','mulai'=>'2022-08-12','berakhir'=>'2022-10-03'],
            ['id'=>2,'tahun'=>2023,'event_id'=>1,'event'=>'LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)','sub_event'=>'LOMBA INOTEK (INOTEK AWARD) 2023','kategori'=>'SEMUA BIDANG','mulai'=>'2023-03-15','berakhir'=>'2023-07-23'],
            ['id'=>3,'tahun'=>2023,'event_id'=>2,'event'=>'INOVASI DAERAH KAB. MAGETAN','sub_event'=>'PELAPORAN INOVASI DAERAH 2023','kategori'=>'SEMUA BIDANG','mulai'=>'2023-09-09','berakhir'=>'2023-12-20'],
            ['id'=>4,'tahun'=>2024,'event_id'=>1,'event'=>'LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)','sub_event'=>'LOMBA INOVASI DAN TEKNOLOGI 2024','kategori'=>'SEMUA','mulai'=>'2024-04-01','berakhir'=>'2025-03-31'],
        ]);
    }

    private static function saveData(array $data): void
    {
        session(['sub_events' => array_values($data)]);
    }

    // ── INDEX ─────────────────────────────────────────────────
    public function index()
    {
        $subEvents = self::getData();
        $events    = EventController::getData();
        return view('master.sub-event', compact('subEvents', 'events'));
    }

    // ── STORE ─────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'event_id'  => 'required|integer',
            'tahun'     => 'required|digits:4',
            'sub_event' => 'required|string|max:255',
            'mulai'     => 'required|date',
            'berakhir'  => 'required|date|after_or_equal:mulai',
        ]);

        $events    = EventController::getData();
        $eventRow  = collect($events)->firstWhere('id', (int) $request->event_id);
        $data      = self::getData();
        $maxId     = count($data) ? max(array_column($data, 'id')) : 0;

        $data[] = [
            'id'        => $maxId + 1,
            'tahun'     => (int) $request->tahun,
            'event_id'  => (int) $request->event_id,
            'event'     => $eventRow['nama_event'] ?? '',
            'sub_event' => $request->sub_event,
            'kategori'  => $request->kategori ?? '',
            'mulai'     => $request->mulai,
            'berakhir'  => $request->berakhir,
        ];

        self::saveData($data);
        return redirect()->route('sub-event.index')->with('success', 'Sub Event berhasil ditambahkan.');
    }

    // ── EDIT (JSON untuk modal) ───────────────────────────────
    public function edit(int $id)
    {
        $item = collect(self::getData())->firstWhere('id', $id);
        return response()->json($item);
    }

    // ── UPDATE ────────────────────────────────────────────────
    public function update(Request $request, int $id)
    {
        $request->validate([
            'event_id'  => 'required|integer',
            'tahun'     => 'required|digits:4',
            'sub_event' => 'required|string|max:255',
            'mulai'     => 'required|date',
            'berakhir'  => 'required|date|after_or_equal:mulai',
        ]);

        $events   = EventController::getData();
        $eventRow = collect($events)->firstWhere('id', (int) $request->event_id);
        $data     = self::getData();

        foreach ($data as &$row) {
            if ($row['id'] == $id) {
                $row['tahun']     = (int) $request->tahun;
                $row['event_id']  = (int) $request->event_id;
                $row['event']     = $eventRow['nama_event'] ?? '';
                $row['sub_event'] = $request->sub_event;
                $row['kategori']  = $request->kategori ?? '';
                $row['mulai']     = $request->mulai;
                $row['berakhir']  = $request->berakhir;
                break;
            }
        }

        self::saveData($data);
        return redirect()->route('sub-event.index')->with('success', 'Sub Event berhasil diperbarui.');
    }

    // ── DESTROY ───────────────────────────────────────────────
    public function destroy(int $id)
    {
        $data = array_filter(self::getData(), fn($r) => $r['id'] != $id);
        self::saveData($data);
        return redirect()->route('sub-event.index')->with('success', 'Sub Event berhasil dihapus.');
    }
}