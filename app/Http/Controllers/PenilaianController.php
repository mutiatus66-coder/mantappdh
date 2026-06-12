<?php
// app/Http/Controllers/PenilaianController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penilai;
use App\Models\Inovator;
use App\Models\Indikator;
use App\Models\KeteranganIndikator;
use App\Models\IndikatorTahap2;
use App\Models\PenilaianUsulan;
use App\Models\PenilaianPemenang;

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

    // ── Helper: ambil penilai_id milik user yang sedang login ─────────────
    private function getPenilaiLogin(): ?object
    {
        return Penilai::where('user_id', auth()->id())->first();
    }

    // ── Helper: ambil inovator dari DB, split per kategori, sertakan nilai ─
    private function getInovatorSplit(int $subEventId): array
    {
        $all = Inovator::query()->where('sub_event_id', $subEventId)
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

        // Load semua nilai Tahap 1 untuk sub event ini
        $inovatorIds = array_column($all, 'id');
        if (!empty($inovatorIds)) {
            $nilaiRows = PenilaianUsulan::whereIn('inovator_id', $inovatorIds)->get();

            // Hitung total nilai per inovator per penilai (avg seluruh indikator)
            // Struktur: [inovator_id][penilai_id] => [nilai...]
            $grouped = [];
            foreach ($nilaiRows as $row) {
                $grouped[$row->inovator_id][$row->penilai_id][] = $row->nilai;
            }

            foreach ($all as &$n) {
                if (isset($grouped[$n['id']])) {
                    $totalAll = [];
                    foreach ($grouped[$n['id']] as $penilaiId => $nilaiArr) {
                        $avg = array_sum($nilaiArr) / count($nilaiArr);
                        $n['nilai'][$penilaiId] = $avg;
                        $totalAll[] = $avg;
                    }
                    $n['total_nilai'] = !empty($totalAll)
                        ? array_sum($totalAll) / count($totalAll)
                        : 0;
                }
            }
            unset($n);
        }

        return [
            'umum'    => array_values(array_filter($all, fn($n) => $n['kategori'] === 'umum')),
            'pelajar' => array_values(array_filter($all, fn($n) => $n['kategori'] === 'pelajar')),
        ];
    }

    // ── Helper: split inovator yang LOLOS saja per kategori (Tahap 2) ─────
    private function getInovatorLolosSplit(int $subEventId): array
    {
        $lolosUmum    = session('tahap1_lolos_' . $subEventId . '_umum');
        $lolosPelajar = session('tahap1_lolos_' . $subEventId . '_pelajar');

        $query = Inovator::query()->where('sub_event_id', $subEventId)->orderBy('inovator');

        $all = $query->get()->map(fn($i) => [
            'id'           => $i->id,
            'inovator'     => $i->inovator,
            'nama_inovasi' => $i->nama_inovasi,
            'kategori'     => $i->kategori,
            'total_nilai'  => 0,
            'nilai'        => [],
        ])->toArray();

        // Load nilai Tahap 2
        $inovatorIds = array_column($all, 'id');
        if (!empty($inovatorIds)) {
            $nilaiRows = PenilaianPemenang::whereIn('inovator_id', $inovatorIds)->get();
            $grouped = [];
            foreach ($nilaiRows as $row) {
                $grouped[$row->inovator_id][$row->penilai_id][] = $row->nilai;
            }

            foreach ($all as &$n) {
                if (isset($grouped[$n['id']])) {
                    $totalAll = [];
                    foreach ($grouped[$n['id']] as $penilaiId => $nilaiArr) {
                        $avg = array_sum($nilaiArr) / count($nilaiArr);
                        $n['nilai'][$penilaiId] = $avg;
                        $totalAll[] = $avg;
                    }
                    $n['total_nilai'] = !empty($totalAll)
                        ? array_sum($totalAll) / count($totalAll)
                        : 0;
                }
            }
            unset($n);
        }

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

        // Terapkan status lolos dari session
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

        // Ambil indikator + keterangan Tahap 1 untuk sub event ini
        $indikators = Indikator::with('subEvent')
            ->where('sub_event_id', $id)
            ->orderBy('id')
            ->get()
            ->map(fn($ind) => [
                'id'             => $ind->id,
                'nama_indikator' => $ind->nama_indikator,
                'keterangans'    => KeteranganIndikator::where('indikator_id', $ind->id)
                    ->orderBy('nilai_minimal')
                    ->get()
                    ->map(fn($k) => [
                        'id'            => $k->id,
                        'keterangan'    => $k->keterangan,
                        'nilai_minimal' => $k->nilai_minimal,
                        'nilai_maksimal'=> $k->nilai_maksimal,
                    ])
                    ->toArray(),
            ])
            ->toArray();

        // Nilai input penilai yang sedang login (untuk pre-fill form)
        $penilaiLogin      = $this->getPenilaiLogin();
        $nilaiLoginPerInovator = [];
        if ($penilaiLogin) {
            $inovatorIds = array_merge(
                array_column($nominasiUmum, 'id'),
                array_column($nominasiPelajar, 'id')
            );
            $rows = PenilaianUsulan::where('penilai_id', $penilaiLogin->id)
                ->whereIn('inovator_id', $inovatorIds)
                ->get();
            foreach ($rows as $row) {
                $nilaiLoginPerInovator[$row->inovator_id][$row->keterangan_indikator_id] = $row->nilai;
            }
        }

        return view('master.penilaian.tahap1.show', [
            'subEvent'              => $subEvent,
            'nominasiUmum'          => $nominasiUmum,
            'nominasiPelajar'       => $nominasiPelajar,
            'penilai'               => $this->getPenilai(),
            'indikators'            => $indikators,
            'penilaiLogin'          => $penilaiLogin,
            'nilaiLoginPerInovator' => $nilaiLoginPerInovator,
        ]);
    }

    /** Simpan lolos ke session (tetap dipertahankan) */
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

    /** Simpan nilai per indikator ke database (penilaian_usulan) */
    public function tahap1SimpanNilai(Request $request, int $id)
    {
        $penilaiLogin = $this->getPenilaiLogin();
        abort_unless($penilaiLogin, 403, 'Anda tidak terdaftar sebagai penilai.');

        $request->validate([
            'inovator_id' => 'required|integer|exists:inovator,id',
            'nilai'       => 'required|array',
            'nilai.*'     => 'integer|min:0|max:100',
        ]);

        $inovatorId = $request->inovator_id;

        // Verifikasi inovator memang milik sub event ini
        $inovator = Inovator::where('id', $inovatorId)
            ->where('sub_event_id', $id)
            ->firstOrFail();

        // upsert tiap keterangan_indikator_id → nilai
        foreach ($request->nilai as $keteranganId => $nilai) {
            PenilaianUsulan::updateOrCreate(
                [
                    'inovator_id'              => $inovator->id,
                    'penilai_id'               => $penilaiLogin->id,
                    'keterangan_indikator_id'  => (int) $keteranganId,
                ],
                ['nilai' => (int) $nilai]
            );
        }

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

        // Ambil indikator Tahap 2 beserta keterangan
        $indikators = IndikatorTahap2::with('keterangans')
            ->where('sub_event_id', $id)
            ->orderBy('jenis')
            ->orderBy('id')
            ->get()
            ->map(fn($ind) => [
                'id'             => $ind->id,
                'nama_indikator' => $ind->nama_indikator,
                'jenis'          => $ind->jenis,
                'keterangans'    => $ind->keterangans
                    ->sortBy('nilai_minimal')
                    ->map(fn($k) => [
                        'id'             => $k->id,
                        'keterangan'     => $k->keterangan,
                        'nilai_minimal'  => $k->nilai_minimal,
                        'nilai_maksimal' => $k->nilai_maksimal,
                    ])
                    ->values()
                    ->toArray(),
            ])
            ->toArray();

        // Nilai input penilai yang sedang login
        $penilaiLogin          = $this->getPenilaiLogin();
        $nilaiLoginPerInovator = [];
        if ($penilaiLogin) {
            $inovatorIds = array_merge(
                array_column($nominasiUmum, 'id'),
                array_column($nominasiPelajar, 'id')
            );
            $rows = PenilaianPemenang::where('penilai_id', $penilaiLogin->id)
                ->whereIn('inovator_id', $inovatorIds)
                ->get();
            foreach ($rows as $row) {
                $nilaiLoginPerInovator[$row->inovator_id][$row->keterangan_tahap2_id] = $row->nilai;
            }
        }

        return view('master.penilaian.tahap2.show', [
            'subEvent'              => $subEvent,
            'nominasiUmum'          => $nominasiUmum,
            'nominasiPelajar'       => $nominasiPelajar,
            'penilai'               => $this->getPenilai(),
            'indikators'            => $indikators,
            'penilaiLogin'          => $penilaiLogin,
            'nilaiLoginPerInovator' => $nilaiLoginPerInovator,
        ]);
    }

    /** Simpan nilai Tahap 2 per indikator ke penilaian_pemenang */
    public function tahap2Simpan(Request $request, int $id)
    {
        $penilaiLogin = $this->getPenilaiLogin();
        abort_unless($penilaiLogin, 403, 'Anda tidak terdaftar sebagai penilai.');

        $request->validate([
            'inovator_id' => 'required|integer|exists:inovator,id',
            'nilai'       => 'required|array',
            'nilai.*'     => 'integer|min:0|max:100',
        ]);

        $inovatorId = $request->inovator_id;

        $inovator = Inovator::where('id', $inovatorId)
            ->where('sub_event_id', $id)
            ->firstOrFail();

        foreach ($request->nilai as $keteranganId => $nilai) {
            PenilaianPemenang::updateOrCreate(
                [
                    'inovator_id'          => $inovator->id,
                    'penilai_id'           => $penilaiLogin->id,
                    'keterangan_tahap2_id' => (int) $keteranganId,
                ],
                ['nilai' => (int) $nilai]
            );
        }

        return response()->json(['success' => true]);
    }
}