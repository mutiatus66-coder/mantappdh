<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BidangController extends Controller
{
    // ── Static data ───────────────────────────────────────────────────────────

    private const BIDANG = [
        1 => [
            ['id' => 1, 'nama' => 'Teknologi Informasi', 'status' => 'aktif'],
            ['id' => 2, 'nama' => 'Pertanian',            'status' => 'tidak_aktif'],
        ],
        2 => [
            ['id' => 3, 'nama' => 'Kesehatan',            'status' => 'aktif'],
            ['id' => 4, 'nama' => 'Pendidikan',           'status' => 'aktif'],
        ],
        3 => [
            ['id' => 5, 'nama' => 'Lingkungan Hidup',     'status' => 'aktif'],
            ['id' => 6, 'nama' => 'Energi Terbarukan',    'status' => 'tidak_aktif'],
        ],
        4 => [
            ['id' => 7,  'nama' => 'Teknologi Informasi', 'status' => 'aktif'],
            ['id' => 8,  'nama' => 'Kesehatan',           'status' => 'aktif'],
            ['id' => 9,  'nama' => 'Pertanian',           'status' => 'aktif'],
            ['id' => 10, 'nama' => 'Pendidikan',          'status' => 'aktif'],
            ['id' => 11, 'nama' => 'Lingkungan Hidup',    'status' => 'aktif'],
        ],
    ];

    private const STATUS_OPTIONS = ['aktif', 'tidak_aktif'];

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function getSubEvents(): array
    {
        return session('sub_events', []);
    }

    private function getData(int $subEventId): array
    {
        return session('bidang_' . $subEventId, self::BIDANG[$subEventId] ?? []);
    }

    private function saveData(int $subEventId, array $data): void
    {
        session(['bidang_' . $subEventId => array_values($data)]);
    }

    private function nextId(int $subEventId): int
    {
        $data = $this->getData($subEventId);

        return count($data) ? max(array_column($data, 'id')) + 1 : 1;
    }

    private function validationRules(): array
    {
        return [
            'sub_event_id' => 'required|integer',
            'nama'         => 'required|string|max:100',
            'status'       => 'required|in:' . implode(',', self::STATUS_OPTIONS),
        ];
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function index()
    {
        return view('master.bidang', [
            'subEvents'  => $this->getSubEvents(),
            'bidangData' => self::BIDANG,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate($this->validationRules());

        $subEventId = (int) $request->sub_event_id;
        $data       = $this->getData($subEventId);

        $data[] = [
            'id'     => $this->nextId($subEventId),
            'nama'   => $request->nama,
            'status' => $request->status,
        ];

        $this->saveData($subEventId, $data);

        return redirect()->route('bidang.index')->with('success', 'Bidang berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        foreach ($this->getSubEvents() as $se) {
            $item = collect($this->getData($se['id']))->firstWhere('id', $id);

            if ($item) {
                return response()->json(array_merge($item, ['sub_event_id' => $se['id']]));
            }
        }

        abort(404);
    }

    public function update(Request $request, int $id)
    {
        $request->validate($this->validationRules());

        $subEventId = (int) $request->sub_event_id;
        $data       = $this->getData($subEventId);

        foreach ($data as &$row) {
            if ($row['id'] === $id) {
                $row['nama']   = $request->nama;
                $row['status'] = $request->status;
                break;
            }
        }
        unset($row);

        $this->saveData($subEventId, $data);

        return redirect()->route('bidang.index')->with('success', 'Bidang berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        foreach ($this->getSubEvents() as $se) {
            $data    = $this->getData($se['id']);
            $filtered = array_filter($data, fn($r) => $r['id'] !== $id);

            if (count($filtered) !== count($data)) {
                $this->saveData($se['id'], $filtered);

                return redirect()->route('bidang.index')->with('success', 'Bidang berhasil dihapus.');
            }
        }

        abort(404);
    }
}