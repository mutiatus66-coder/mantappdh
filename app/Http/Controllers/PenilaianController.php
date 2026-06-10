<?php
// app/Http/Controllers/PenilaianController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penilai;
use App\Models\Inovator;

class PenilaianController extends Controller
{
    // ── Helper: ambil sub events ──────────────────────────────────────────
    private function getSubEvents(): array
    {
        return session('sub_events', []);
    }

    // ── Helper: ambil penilai dari DB ─────────────────────────────────────
    private function getPenilai(): array
    {
        return Penilai::orderBy('nama')
            ->get()
            ->map(fn($p) => [
                'id'           => $p->id,
                'nama'         => $p->nama,
                'nama_singkat' => explode(' ', trim($p->nama))[0],
            ])
            ->toArray();
    }

    // ── Helper: ambil inovator dari DB, split per kategori ────────────────
    private function getInovatorSplit(int $subEventId): array
    {
        $all = Inovator::where('sub_event_id', $subEventId)
            ->orderBy('inovator')
            ->get()
            ->map(fn($i) => [
                'id'           => $i->id,
                'inovator'     => $i->inovator,
                'nama_inovasi' => $i->nama_inovasi,
                'kategori'     => $i->kategori,
                'lolos'        => false,
                'total_nilai'  => 0,
                'nilai'        => [],
            ])
            ->toArray();

        return [
            'umum'    => array_values(array_filter($all, fn($n) => $n['kategori'] === 'umum')),
            'pelajar' => array_values(array_filter($all, fn($n) => $n['kategori'] === 'pelajar')),
        ];
    }

    /**
     * Helper: split inovator yang LOLOS saja per kategori (Tahap 2).
     *
     * Prioritas:
     *   1. Session (hasil simpan Tahap 1 oleh admin)
     *   2. Semua inovator pada sub event (fallback jika belum ada simpan)
     */
    private function getInovatorLolosSplit(int $subEventId): array
    {
        $lolosUmum    = session('tahap1_lolos_' . $subEventId . '_umum');
        $lolosPelajar = session('tahap1_lolos_' . $subEventId . '_pelajar');

        $query = Inovator::where('sub_event_id', $subEventId)->orderBy('inovator');

        $all = $query->get()->map(fn($i) => [
            'id'           => $i->id,
            'inovator'     => $i->inovator,
            'nama_inovasi' => $i->nama_inovasi,
            'kategori'     => $i->kategori,
            'total_nilai'  => 0,
            'nilai'        => [],
        ])->toArray();

        $filter = fn(array $items, string $kat, ?array $ids): array =>
            array_values(array_filter($items, function ($n) use ($kat, $ids) {
                if ($n['kategori'] !== $kat) return false;
                return $ids !== null ? in_array($n['id'], $ids, true) : true;
            }));

        return [
            'umum'    => $filter($all, 'umum',    $lolosUmum),
            'pelajar' => $filter($all, 'pelajar', $lolosPelajar),
        ];
    }

    // ── Helper: build nominasiData untuk index views ──────────────────────
    private function buildNominasiData(array $subEvents): array
    {
        $subEventIds  = array_column($subEvents, 'id');
        $allInovators = Inovator::whereIn('sub_event_id', $subEventIds)
            ->orderBy('inovator')
            ->get();

        $nominasiData = [];
        foreach ($subEvents as $se) {
            $nominasiData[$se['id']] = $allInovators
                ->where('sub_event_id', $se['id'])
                ->map(fn($i) => [
                    'id'           => $i->id,
                    'inovator'     => $i->inovator,
                    'nama_inovasi' => $i->nama_inovasi,
                    'kategori'     => $i->kategori,
                ])
                ->values()
                ->toArray();
        }

        return $nominasiData;
    }

    // ==================== TAHAP 1 ====================

    public function tahap1()
    {
        $subEvents    = $this->getSubEvents();
        $nominasiData = $this->buildNominasiData($subEvents);

        return view('master.penilaian.tahap1.index', compact('subEvents', 'nominasiData'));
    }

    public function tahap1Show(int $id)
    {
        $subEvent = collect($this->getSubEvents())->firstWhere('id', $id);
        abort_unless($subEvent, 404);

        ['umum' => $nominasiUmum, 'pelajar' => $nominasiPelajar] = $this->getInovatorSplit($id);

        // Terapkan status lolos dari session jika admin sudah pernah simpan
        $lolosUmum    = session('tahap1_lolos_' . $id . '_umum');
        $lolosPelajar = session('tahap1_lolos_' . $id . '_pelajar');

        if ($lolosUmum !== null) {
            foreach ($nominasiUmum as &$n) {
                $n['lolos'] = in_array($n['id'], $lolosUmum, true);
            }
            unset($n);
        }

        if ($lolosPelajar !== null) {
            foreach ($nominasiPelajar as &$n) {
                $n['lolos'] = in_array($n['id'], $lolosPelajar, true);
            }
            unset($n);
        }

        return view('master.penilaian.tahap1.show', [
            'subEvent'        => $subEvent,
            'nominasiUmum'    => $nominasiUmum,
            'nominasiPelajar' => $nominasiPelajar,
            'penilai'         => $this->getPenilai(),
        ]);
    }

    public function tahap1Simpan(Request $request, int $id)
    {
        $request->validate([
            'kategori' => 'required|in:umum,pelajar',
            'ids'      => 'array',
            'ids.*'    => 'integer',
        ]);

        session(['tahap1_lolos_' . $id . '_' . $request->kategori => $request->ids ?? []]);

        return response()->json(['success' => true]);
    }

    // ==================== TAHAP 2 ====================

    public function tahap2()
    {
        $subEvents    = $this->getSubEvents();
        $nominasiData = $this->buildNominasiData($subEvents);

        return view('master.penilaian.tahap2.index', compact('subEvents', 'nominasiData'));
    }

    public function tahap2Show(int $id)
    {
        $subEvent = collect($this->getSubEvents())->firstWhere('id', $id);
        abort_unless($subEvent, 404);

        ['umum' => $nominasiUmum, 'pelajar' => $nominasiPelajar] = $this->getInovatorLolosSplit($id);

        return view('master.penilaian.tahap2.show', [
            'subEvent'        => $subEvent,
            'nominasiUmum'    => $nominasiUmum,
            'nominasiPelajar' => $nominasiPelajar,
            'penilai'         => $this->getPenilai(),
        ]);
    }
}