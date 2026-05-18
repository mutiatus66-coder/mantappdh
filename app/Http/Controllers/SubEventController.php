<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class SubEventController extends Controller
{
    // ── Static Data Sub Events ────────────────────────────────────────────
    private static array $data = [
        ['id' => 1, 'tahun' => 2022, 'event' => 'LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)', 'sub_event' => 'LOMBA INOTEK 2022',                             'kategori' => 'SEMUA BIDANG', 'mulai' => '2022-08-12', 'berakhir' => '2022-10-03'],
        ['id' => 2, 'tahun' => 2023, 'event' => 'LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)', 'sub_event' => 'LOMBA INOTEK (INOTEK AWARD) 2023',               'kategori' => 'SEMUA BIDANG', 'mulai' => '2023-03-15', 'berakhir' => '2023-07-23'],
        ['id' => 3, 'tahun' => 2023, 'event' => 'INOVASI DAERAH KAB. MAGETAN',                'sub_event' => 'PELAPORAN INOVASI DAERAH 2023',                  'kategori' => 'SEMUA BIDANG', 'mulai' => '2023-09-09', 'berakhir' => '2023-12-20'],
        ['id' => 4, 'tahun' => 2024, 'event' => 'LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)', 'sub_event' => 'LOMBA INOVASI DAN TEKNOLOGI 2024',               'kategori' => 'SEMUA',        'mulai' => '2024-04-01', 'berakhir' => '2025-03-31'],
        ['id' => 5, 'tahun' => 2024, 'event' => 'INOVASI DAERAH KAB. MAGETAN',                'sub_event' => 'PELAPORAN INOVASI DAERAH 2024 & INODA AWARD 2025','kategori' => 'SEMUA BIDANG', 'mulai' => '2024-09-01', 'berakhir' => '2025-02-28'],
        ['id' => 6, 'tahun' => 2025, 'event' => 'PAMERAN INOVASI DAN TEKNOLOGI',              'sub_event' => 'PAMERAN INOTEK 2025',                            'kategori' => 'SEMUA BIDANG', 'mulai' => '2025-01-10', 'berakhir' => '2025-06-30'],
        ['id' => 7, 'tahun' => 2025, 'event' => 'KOMPETISI INOVASI DIGITAL',                  'sub_event' => 'KOMPETISI INOVASI DIGITAL 2025',                 'kategori' => 'TEKNOLOGI',    'mulai' => '2025-03-01', 'berakhir' => '2025-08-31'],
    ];

    // ── Helpers ───────────────────────────────────────────────────────────

    private function getData(): array
    {
        return session('sub_events', self::$data);
    }

    private function saveData(array $data): void
    {
        session(['sub_events' => array_values($data)]);
    }

    private function nextId(array $data): int
    {
        return count($data) ? max(array_column($data, 'id')) + 1 : 1;
    }

    private function validationRules(): array
    {
        return [
            'event'     => 'required|string|exists:events,nama_event',
            'tahun'     => 'required|digits:4',
            'sub_event' => 'required|string|max:255',
            'kategori'  => 'nullable|string|max:100',
            'mulai'     => 'required|date',
            'berakhir'  => 'required|date|after_or_equal:mulai',
        ];
    }

    private function mapRequest(Request $request): array
    {
        return [
            'tahun'     => (int) $request->tahun,
            'event'     => $request->event,
            'sub_event' => $request->sub_event,
            'kategori'  => $request->kategori ?? '',
            'mulai'     => $request->mulai,
            'berakhir'  => $request->berakhir,
        ];
    }

    private function getEvents(): array
    {
        return Event::query()
                    ->orderBy('nama_event')
                    ->pluck('nama_event')
                    ->toArray();
    }

    // ── CRUD ──────────────────────────────────────────────────────────────

    public function index()
    {
        return view('master.sub-event', [
            'subEvents' => $this->getData(),
            'events'    => $this->getEvents(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate($this->validationRules());

        $data   = $this->getData();
        $data[] = array_merge(['id' => $this->nextId($data)], $this->mapRequest($request));

        $this->saveData($data);

        return redirect()->route('sub-event.index')
                         ->with('success', 'Sub Event berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $item = collect($this->getData())->firstWhere('id', $id);

        abort_unless($item, 404);

        return response()->json($item);
    }

    public function update(Request $request, int $id)
    {
        $request->validate($this->validationRules());

        $data = $this->getData();

        foreach ($data as &$row) {
            if ($row['id'] === $id) {
                $row = array_merge($row, $this->mapRequest($request));
                break;
            }
        }

        $this->saveData($data);

        return redirect()->route('sub-event.index')
                         ->with('success', 'Sub Event berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $data = array_filter($this->getData(), fn($r) => $r['id'] !== $id);

        $this->saveData($data);

        return redirect()->route('sub-event.index')
                         ->with('success', 'Sub Event berhasil dihapus.');
    }
}