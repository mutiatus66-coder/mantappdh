<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penilai;
use App\Models\Usulan;
use App\Models\SubEvent;
use App\Models\indikator       as Indikator;
use App\Models\KeteranganIndikator;
use App\Models\IndikatorTahap2;
use App\Models\PenilaianUsulan;
use App\Models\Pemenang;

class PenilaianController extends Controller
{
    // ── Helper: ambil sub events dari DB ──────────────────────────────────
    private function getSubEvents(): array
    {
        return SubEvent::with('event')
            ->orderBy('tahun', 'desc')
            ->get()
            ->map(fn($se) => [
                'id'        => $se->id,
                'sub_event' => $se->sub_event,
                'tahun'     => $se->tahun,
                'event'     => $se->event?->nama_event ?? '-',
            ])
            ->toArray();
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

    // ── Helper: ambil penilai milik user yang sedang login ────────────────
    private function getPenilaiLogin(): ?object
    {
        return Penilai::where('user_id', auth()->id())->first();
    }

    // ── Helper: ambil usulan submitted, split per kategori, sertakan nilai ─
    private function getUsulanSplit(int $subEventId): array
    {
        $all = Usulan::forSubEvent($subEventId)
            ->submitted()
            ->orderBy('inovator')
            ->get()
            ->map(fn($u) => [
                'id'           => $u->id,
                'inovator'     => $u->inovator,
                'nama_inovasi' => $u->nama_inovasi,
                'kategori'     => $u->kategori,
                'lolos'        => false,
                'total_nilai'  => 0,
                'nilai'        => [],
            ])
            ->toArray();

        // Load semua nilai Tahap 1 untuk sub event ini
        $usulanIds = array_column($all, 'id');
        if (!empty($usulanIds)) {
            $nilaiRows = PenilaianUsulan::whereIn('usulan_id', $usulanIds)->get();

            $grouped = [];
            foreach ($nilaiRows as $row) {
                $grouped[$row->usulan_id][$row->penilai_id][] = $row->nilai;
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

    // ── Helper: split usulan LOLOS saja per kategori (Tahap 2) ───────────
    private function getUsulanLolosSplit(int $subEventId): array
    {
        $lolosUmum    = session('tahap1_lolos_' . $subEventId . '_umum');
        $lolosPelajar = session('tahap1_lolos_' . $subEventId . '_pelajar');

        $all = Usulan::forSubEvent($subEventId)
            ->submitted()
            ->orderBy('inovator')
            ->get()
            ->map(fn($u) => [
                'id'           => $u->id,
                'inovator'     => $u->inovator,
                'nama_inovasi' => $u->nama_inovasi,
                'kategori'     => $u->kategori,
                'total_nilai'  => 0,
                'nilai'        => [],
            ])
            ->toArray();

        // Load nilai Tahap 2
        $usulanIds = array_column($all, 'id');
        if (!empty($usulanIds)) {
            $nilaiRows = PenilaianUsulan::whereIn('usulan_id', $usulanIds)->get();
            $grouped = [];
            foreach ($nilaiRows as $row) {
                $grouped[$row->usulan_id][$row->penilai_id][] = $row->nilai;
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
        $subEventIds = array_column($subEvents, 'id');

        $allUsulan = Usulan::whereIn('sub_event_id', $subEventIds)
            ->submitted()
            ->orderBy('inovator')
            ->get();

        $nominasiData = [];
        foreach ($subEvents as $se) {
            $nominasiData[$se['id']] = $allUsulan
                ->where('sub_event_id', $se['id'])
                ->map(fn($u) => [
                    'id'           => $u->id,
                    'inovator'     => $u->inovator,
                    'nama_inovasi' => $u->nama_inovasi,
                    'kategori'     => $u->kategori,
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
        $subEvent = SubEvent::findOrFail($id);
        $seArr = ['id' => $subEvent->id, 'sub_event' => $subEvent->sub_event, 'tahun' => $subEvent->tahun];

        ['umum' => $nominasiUmum, 'pelajar' => $nominasiPelajar] = $this->getUsulanSplit($id);

        $lolosUmum    = session('tahap1_lolos_' . $id . '_umum');
        $lolosPelajar = session('tahap1_lolos_' . $id . '_pelajar');

        if ($lolosUmum !== null) {
            foreach ($nominasiUmum as &$n) { $n['lolos'] = in_array($n['id'], $lolosUmum, true); }
            unset($n);
        }
        if ($lolosPelajar !== null) {
            foreach ($nominasiPelajar as &$n) { $n['lolos'] = in_array($n['id'], $lolosPelajar, true); }
            unset($n);
        }

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
                        'id'             => $k->id,
                        'keterangan'     => $k->keterangan,
                        'nilai_minimal'  => $k->nilai_minimal,
                        'nilai_maksimal' => $k->nilai_maksimal,
                    ])
                    ->toArray(),
            ])
            ->toArray();

        $penilaiLogin = $this->getPenilaiLogin();
        $nilaiLoginPerUsulan = [];
        if ($penilaiLogin) {
            $usulanIds = array_merge(
                array_column($nominasiUmum, 'id'),
                array_column($nominasiPelajar, 'id')
            );
            $rows = PenilaianUsulan::where('penilai_id', $penilaiLogin->id)
                ->whereIn('usulan_id', $usulanIds)
                ->get();
            foreach ($rows as $row) {
                $nilaiLoginPerUsulan[$row->usulan_id][$row->keterangan_indikator_id] = $row->nilai;
            }
        }

        return view('master.penilaian.tahap1.show', [
            'subEvent'              => $seArr,
            'nominasiUmum'          => $nominasiUmum,
            'nominasiPelajar'       => $nominasiPelajar,
            'penilai'               => $this->getPenilai(),
            'indikators'            => $indikators,
            'penilaiLogin'          => $penilaiLogin,
            'nilaiLoginPerInovator' => $nilaiLoginPerUsulan, // nama var dipertahankan agar view tidak error
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

    public function tahap1SimpanNilai(Request $request, int $id)
    {
        $penilaiLogin = $this->getPenilaiLogin();
        abort_unless($penilaiLogin, 403, 'Anda tidak terdaftar sebagai penilai.');

        $request->validate([
            'usulan_id' => 'required|integer|exists:usulans,id',
            'nilai'     => 'required|array',
            'nilai.*'   => 'integer|min:0|max:100',
        ]);

        $usulan = Usulan::where('id', $request->usulan_id)
            ->where('sub_event_id', $id)
            ->firstOrFail();

        foreach ($request->nilai as $keteranganId => $nilai) {
            PenilaianUsulan::updateOrCreate(
                [
                    'usulan_id'               => $usulan->id,
                    'penilai_id'              => $penilaiLogin->id,
                    'keterangan_indikator_id' => (int) $keteranganId,
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
        $subEvent = SubEvent::findOrFail($id);
        $seArr = ['id' => $subEvent->id, 'sub_event' => $subEvent->sub_event, 'tahun' => $subEvent->tahun];

        ['umum' => $nominasiUmum, 'pelajar' => $nominasiPelajar] = $this->getUsulanLolosSplit($id);

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

        $penilaiLogin = $this->getPenilaiLogin();
        $nilaiLoginPerUsulan = [];
        if ($penilaiLogin) {
            $usulanIds = array_merge(
                array_column($nominasiUmum, 'id'),
                array_column($nominasiPelajar, 'id')
            );
            $rows = Pemenang::where('penilai_id', $penilaiLogin->id)
                ->whereIn('usulan_id', $usulanIds)
                ->get();
            foreach ($rows as $row) {
                $nilaiLoginPerUsulan[$row->usulan_id][$row->keterangan_tahap2_id] = $row->nilai;
            }
        }

        return view('master.penilaian.tahap2.show', [
            'subEvent'              => $seArr,
            'nominasiUmum'          => $nominasiUmum,
            'nominasiPelajar'       => $nominasiPelajar,
            'penilai'               => $this->getPenilai(),
            'indikators'            => $indikators,
            'penilaiLogin'          => $penilaiLogin,
            'nilaiLoginPerInovator' => $nilaiLoginPerUsulan,
        ]);
    }

    public function tahap2Simpan(Request $request, int $id)
    {
        $penilaiLogin = $this->getPenilaiLogin();
        abort_unless($penilaiLogin, 403, 'Anda tidak terdaftar sebagai penilai.');

        $request->validate([
            'usulan_id' => 'required|integer|exists:usulans,id',
            'nilai'     => 'required|array',
            'nilai.*'   => 'integer|min:0|max:100',
        ]);

        $usulan = Usulan::where('id', $request->usulan_id)
            ->where('sub_event_id', $id)
            ->firstOrFail();

        foreach ($request->nilai as $keteranganId => $nilai) {
            Pemenang::updateOrCreate(
                [
                    'usulan_id'            => $usulan->id,
                    'penilai_id'           => $penilaiLogin->id,
                    'keterangan_tahap2_id' => (int) $keteranganId,
                ],
                ['nilai' => (int) $nilai]
            );
        }

        return response()->json(['success' => true]);
    }
}