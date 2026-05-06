<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BidangController extends Controller
{
    /**
     * Static seed data — keyed by sub_event_id.
     * Each entry is an array of bidang rows.
     */
    private static array $seed = [
        1 => [
            ['id' => 1, 'sub_event_id' => 1, 'nama' => 'semua bidang', 'status' => 'tidak_aktif'],
        ],
        2 => [
            ['id' => 2, 'sub_event_id' => 2, 'nama' => 'Teknologi Informasi', 'status' => 'aktif'],
            ['id' => 3, 'sub_event_id' => 2, 'nama' => 'Energi Terbarukan',   'status' => 'aktif'],
        ],
        3 => [
            ['id' => 4, 'sub_event_id' => 3, 'nama' => 'Inovasi Sosial', 'status' => 'aktif'],
        ],
        4 => [],
    ];

    /* ─── sub-event lookup (same seed as Admin controller) ─── */
    private static array $subEventSeed = [
        ['id' => 1, 'sub_event' => 'LOMBA INOTEK 2022'],
        ['id' => 2, 'sub_event' => 'LOMBA INOTEK (INOTEK AWARD) 2023'],
        ['id' => 3, 'sub_event' => 'PELAPORAN INOVASI DAERAH 2023'],
        ['id' => 4, 'sub_event' => 'LOMBA INOVASI DAN TEKNOLOGI 2024'],
    ];

    /* ════════════════════════════════════════════════════════
     *  Session helpers
     * ════════════════════════════════════════════════════════ */
    private function getData(): array
    {
        return session('bidang_data', self::$seed);
    }

    private function saveData(array $data): void
    {
        session(['bidang_data' => $data]);
    }

    private function getSubEvents(): array
    {
        return session('sub_events_list', self::$subEventSeed);
    }

    /* ════════════════════════════════════════════════════════
     *  CRUD
     * ════════════════════════════════════════════════════════ */

    /**
     * GET /admin/bidang
     * Show all sub-events with their bidang lists.
     */
    public function index()
    {
        $subEvents  = $this->getSubEvents();
        $bidangData = $this->getData();          // array keyed by sub_event_id

        return view('master.bidang', compact('subEvents', 'bidangData'));
    }

    /**
     * POST /admin/bidang
     * Store a new bidang entry.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sub_event_id' => 'required|integer',
            'nama'         => 'required|string|max:255',
            'status'       => 'required|in:aktif,tidak_aktif',
        ]);

        $data        = $this->getData();
        $subEventId  = (int) $request->sub_event_id;
        $allRows     = array_merge(...array_values($data));
        $maxId       = count($allRows) ? max(array_column($allRows, 'id')) : 0;

        $data[$subEventId][] = [
            'id'           => $maxId + 1,
            'sub_event_id' => $subEventId,
            'nama'         => $request->nama,
            'status'       => $request->status,
        ];

        $this->saveData($data);

        return redirect()
            ->route('admin.bidang.index', ['open' => $subEventId])
            ->with('success', 'Bidang berhasil ditambahkan.');
    }

    /**
     * GET /admin/bidang/{id}/edit  (AJAX)
     * Return JSON for the edit modal.
     */
    public function edit(int $id)
    {
        foreach ($this->getData() as $rows) {
            foreach ($rows as $row) {
                if ($row['id'] === $id) {
                    return response()->json($row);
                }
            }
        }
        abort(404);
    }

    /**
     * PUT /admin/bidang/{id}
     * Update an existing bidang entry.
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'nama'   => 'required|string|max:255',
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        $data = $this->getData();
        $subEventId = null;

        foreach ($data as $seId => &$rows) {
            foreach ($rows as &$row) {
                if ($row['id'] === $id) {
                    $row['nama']   = $request->nama;
                    $row['status'] = $request->status;
                    $subEventId    = $seId;
                    break 2;
                }
            }
        }

        $this->saveData($data);

        return redirect()
            ->route('admin.bidang.index', ['open' => $subEventId])
            ->with('success', 'Bidang berhasil diperbarui.');
    }

    /**
     * DELETE /admin/bidang/{id}
     * Remove a bidang entry.
     */
    public function destroy(int $id)
    {
        $data = $this->getData();
        $subEventId = null;

        foreach ($data as $seId => &$rows) {
            foreach ($rows as $key => $row) {
                if ($row['id'] === $id) {
                    unset($rows[$key]);
                    $rows       = array_values($rows);
                    $subEventId = $seId;
                    break 2;
                }
            }
        }

        $this->saveData($data);

        return redirect()
            ->route('admin.bidang.index', ['open' => $subEventId])
            ->with('success', 'Bidang berhasil dihapus.');
    }
}