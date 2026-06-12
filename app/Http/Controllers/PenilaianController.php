<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Usulan;
use App\Models\SubEvent;
use App\Models\Indikator;
use App\Models\KeteranganIndikator;
use App\Models\IndikatorTahap2;
use App\Models\Penilaian;
use App\Models\Pemenang;

class PenilaianController extends Controller
{
    // ── Helper: ambil penilai (user dengan hak_akses = penilai) ──────────
    private function getPenilai(): array
    {
        return User::where('hak_akses', 'penilai')
            ->orderBy('nama')
            ->get()
            ->map(fn($p) => [
                'id'           => $p->id,
                'nama'         => $p->nama,
                'nama_singkat' => explode(' ', trim($p->nama))[0],
            ])
            ->toArray();
    }

    // ── Helper: user penilai yang sedang login ────────────────────────────
    private function getPenilaiLogin(): ?object
    {
        $user = auth()->user();
        if ($user && $user->hak_akses === 'penilai') {
            return $user;
        }
        return null;
    }

    // ── Helper: build nominasiData untuk index views ──────────────────────
    private function buildNominasiData($subEvents): array
    {
        $nominasiData = [];
        foreach ($subEvents as $se) {
            $nominasiData[$se->id] = Usulan::where('sub_event_id', $se->id)
                ->orderBy('inovator')
                ->get()
                ->map(fn($u) => [
                    'id'           => $u->id,
                    'inovator'     => $u->inovator,
                    'nama_inovasi' => $u->nama_inovasi,
                    'kategori'     => $u->kategori ?? 'umum',
                ])
                ->toArray();
        }
        return $nominasiData;
    }

    // ── Helper: split usulan per kategori + sertakan nilai ────────────────
    private function getUsulanSplit(int $subEventId): array
    {
        $all = Usulan::where('sub_event_id', $subEventId)
            ->orderBy('inovator')
            ->get()
            ->map(fn($u) => [
                'id'           => $u->id,
                'inovator'     => $u->inovator,
                'nama_inovasi' => $u->nama_inovasi,
                'kategori'     => $u->kategori ?? 'umum',
                'status'       => $u->status,
                'lolos'        => $u->status === 'Lolos Tahap 1',
                'total_nilai'  => 0,
                'nilai'        => [],
            ])
            ->toArray();

        // Load nilai tahap 1
        $usulanIds = array_column($all, 'id');
        if (!empty($usulanIds)) {
            $nilaiRows = Penilaian::whereIn('usulan_id', $usulanIds)->get();
            $grouped   = [];
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

    // ── Helper: split usulan yang LOLOS saja (Tahap 2) ───────────────────
    private function getUsulanLolosSplit(int $subEventId): array
    {
        $all = Usulan::where('sub_event_id', $subEventId)
            ->where('status', 'Lolos Tahap 1')
            ->orderBy('inovator')
            ->get()
            ->map(fn($u) => [
                'id'           => $u->id,
                'inovator'     => $u->inovator,
                'nama_inovasi' => $u->nama_inovasi,
                'kategori'     => $u->kategori ?? 'umum',
                'total_nilai'  => 0,
                'nilai'        => [],
            ])
            ->toArray();

        // Load nilai tahap 2
        $usulanIds = array_column($all, 'id');
        if (!empty($usulanIds)) {
            $nilaiRows = Pemenang::whereIn('usulan_id', $usulanIds)->get();
            $grouped   = [];
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

    // ══════════════════════════════════════════════════════════
    // TAHAP 1
    // ══════════════════════════════════════════════════════════

    public function tahap1()
    {
        $subEvents    = SubEvent::orderBy('tahun', 'desc')->get();
        $nominasiData = $this->buildNominasiData($subEvents);

        return view('master.penilaian.tahap1.index', compact('subEvents', 'nominasiData'));
    }

    public function tahap1Show(int $id)
    {
        $subEvent = SubEvent::findOrFail($id);

        ['umum' => $nominasiUmum, 'pelajar' => $nominasiPelajar] = $this->getUsulanSplit($id);

        $indikators = Indikator::where('sub_event_id', $id)
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

        $penilaiLogin          = $this->getPenilaiLogin();
        $nilaiLoginPerUsulan   = [];
        if ($penilaiLogin) {
            $usulanIds = array_merge(
                array_column($nominasiUmum, 'id'),
                array_column($nominasiPelajar, 'id')
            );
            $rows = Penilaian::where('penilai_id', $penilaiLogin->id)
                ->whereIn('usulan_id', $usulanIds)
                ->get();
            foreach ($rows as $row) {
                $nilaiLoginPerUsulan[$row->usulan_id][$row->keterangan_indikator_id] = $row->nilai;
            }
        }

        return view('master.penilaian.tahap1.show', [
            'subEvent'               => $subEvent,
            'nominasiUmum'           => $nominasiUmum,
            'nominasiPelajar'        => $nominasiPelajar,
            'penilai'                => $this->getPenilai(),
            'indikators'             => $indikators,
            'penilaiLogin'           => $penilaiLogin,
            'nilaiLoginPerUsulan'    => $nilaiLoginPerUsulan,
            'nilaiLoginPerInovator'  => $nilaiLoginPerUsulan, // alias untuk kompatibilitas blade lama
        ]);
    }

    public function tahap1Simpan(Request $request, int $id)
    {
        $request->validate([
            'kategori' => 'required|in:umum,pelajar',
            'ids'      => 'array',
            'ids.*'    => 'integer',
        ]);

        $lolosIds = $request->ids ?? [];

        // Reset semua di sub event + kategori ini
        Usulan::where('sub_event_id', $id)
              ->where('kategori', $request->kategori)
              ->update(['status' => 'Tidak Lolos Tahap 1']);

        // Set yang terpilih jadi lolos
        if (!empty($lolosIds)) {
            Usulan::whereIn('id', $lolosIds)
                  ->where('sub_event_id', $id)
                  ->update(['status' => 'Lolos Tahap 1']);
        }

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
            Penilaian::updateOrCreate(
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

    // ══════════════════════════════════════════════════════════
    // TAHAP 2
    // ══════════════════════════════════════════════════════════

    public function tahap2()
    {
        $subEvents    = SubEvent::orderBy('tahun', 'desc')->get();
        $nominasiData = [];
        foreach ($subEvents as $se) {
            $nominasiData[$se->id] = Usulan::where('sub_event_id', $se->id)
                ->where('status', 'Lolos Tahap 1')
                ->get()->toArray();
        }

        return view('master.penilaian.tahap2.index', compact('subEvents', 'nominasiData'));
    }

    public function tahap2Show(int $id)
    {
        $subEvent = SubEvent::findOrFail($id);

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

        $penilaiLogin          = $this->getPenilaiLogin();
        $nilaiLoginPerUsulan   = [];
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
            'subEvent'               => $subEvent,
            'nominasiUmum'           => $nominasiUmum,
            'nominasiPelajar'        => $nominasiPelajar,
            'penilai'                => $this->getPenilai(),
            'indikators'             => $indikators,
            'penilaiLogin'           => $penilaiLogin,
            'nilaiLoginPerUsulan'    => $nilaiLoginPerUsulan,
            'nilaiLoginPerInovator'  => $nilaiLoginPerUsulan, // alias untuk kompatibilitas blade lama
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