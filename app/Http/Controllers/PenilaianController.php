<?php
// app/Http/Controllers/PenilaianController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Penilai;
use App\Models\Usulan;
use App\Models\SubEvent;
use App\Models\Indikator;
use App\Models\KeteranganIndikator;
use App\Models\IndikatorTahap2;
use App\Models\KeteranganTahap2;
use App\Models\FormulasiTahap1;
use App\Models\FormulasiTahap2;
use App\Models\PenilaianUsulan;
use App\Models\Pemenang;
use App\Models\CatatanPenilai;
use App\Models\RankingTahap2;

class PenilaianController extends Controller
{
    // ===================== HELPERS - UMUM =====================

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

    private function getPenilaiForSubEvent(int $subEventId): array
    {
        return Penilai::where('sub_event_id', $subEventId)
            ->orderBy('nama')
            ->get()
            ->map(fn($p) => [
                'id'           => $p->id,
                'nama'         => $p->nama,
                'nama_singkat' => $this->namaSingkat($p->nama),
            ])
            ->toArray();
    }

    private function namaSingkat(?string $nama): string
    {
        $nama = trim((string) $nama);
        if ($nama === '') return '-';

        $titles = [
            'dr', 'drs', 'dra', 'ir', 'prof', 'h', 'hj', 'kh',
            'st', 'se', 'mm', 'mt', 'msi', 'spd', 'mpd', 'phd',
        ];

        $parts = preg_split('/\s+/', $nama);
        foreach ($parts as $p) {
            $clean = strtolower(rtrim($p, '.,'));
            if ($clean === '' || in_array($clean, $titles, true)) {
                continue;
            }
            return $p;
        }
        return $parts[0] ?? $nama;
    }

    private function getPenilaiLogin(int $subEventId): ?object
    {
        return Penilai::where('user_id', auth()->id())
            ->where('sub_event_id', $subEventId)
            ->first();
    }

    // ===================== HELPERS - FORMULASI & NILAI =====================

    private function canonJenisTahap1(?string $jenis): string
    {
        $j = strtolower(trim((string) $jenis));
        return str_contains($j, 'makalah') ? 'makalah' : 'substansi';
    }

    private function canonJenisTahap2(?string $jenis): string
    {
        $j = strtolower(trim((string) $jenis));
        return str_contains($j, 'perag') ? 'Peragaan' : 'Subtansi Inovasi';
    }

    private function mapKeteranganJenisTahap1(int $subEventId): array
    {
        $indikatorJenis = Indikator::where('sub_event_id', $subEventId)
            ->pluck('jenis', 'id')
            ->toArray();

        if (empty($indikatorJenis)) return [];

        return KeteranganIndikator::whereIn('indikator_id', array_keys($indikatorJenis))
            ->get()
            ->mapWithKeys(fn($k) => [
                $k->id => $this->canonJenisTahap1($indikatorJenis[$k->indikator_id] ?? null),
            ])
            ->toArray();
    }

    private function mapKeteranganJenisTahap2(int $subEventId): array
    {
        $indikatorJenis = IndikatorTahap2::where('sub_event_id', $subEventId)
            ->pluck('jenis', 'id')
            ->toArray();

        if (empty($indikatorJenis)) return [];

        return KeteranganTahap2::whereIn('indikator_tahap2_id', array_keys($indikatorJenis))
            ->get()
            ->mapWithKeys(fn($k) => [
                $k->id => $this->canonJenisTahap2($indikatorJenis[$k->indikator_tahap2_id] ?? null),
            ])
            ->toArray();
    }

    private function hitungNilaiBerbobot(array $perJenis, array $bobot, bool $adaBobot): float
    {
        $avgPerJenis = [];
        foreach ($perJenis as $jenis => $nilaiArr) {
            $avgPerJenis[$jenis] = count($nilaiArr) > 0
                ? array_sum($nilaiArr) / count($nilaiArr)
                : 0;
        }

        if ($adaBobot) {
            $totalNilai = 0;
            $totalBobot = 0;
            foreach ($bobot as $jenis => $bobotNilai) {
                if ($bobotNilai <= 0) continue;
                if (isset($avgPerJenis[$jenis])) {
                    $totalNilai += $avgPerJenis[$jenis] * $bobotNilai;
                    $totalBobot += $bobotNilai;
                }
            }
            return $totalBobot > 0 ? ($totalNilai / $totalBobot) : 0;
        }

        if (empty($perJenis)) return 0;
        $semua = array_merge(...array_values($perJenis));
        return count($semua) > 0 ? array_sum($semua) / count($semua) : 0;
    }

    // ===================== HELPERS - STATUS =====================

    private function semuaPenilaiSudahMenilaiTahap1(int $usulanId, int $subEventId): bool
    {
        $penilaiIds    = Penilai::where('sub_event_id', $subEventId)->pluck('id');
        $jumlahPenilai = $penilaiIds->count();
        if ($jumlahPenilai === 0) return false;

        $jumlahSudah = PenilaianUsulan::where('usulan_id', $usulanId)
            ->whereIn('penilai_id', $penilaiIds)
            ->distinct('penilai_id')
            ->count('penilai_id');

        return $jumlahSudah >= $jumlahPenilai;
    }

    private function semuaPenilaiSudahMenilaiTahap2(int $usulanId, int $subEventId): bool
    {
        $penilaiIds    = Penilai::where('sub_event_id', $subEventId)->pluck('id');
        $jumlahPenilai = $penilaiIds->count();
        if ($jumlahPenilai === 0) return false;

        $jumlahSudah = Pemenang::where('usulan_id', $usulanId)
            ->whereIn('penilai_id', $penilaiIds)
            ->distinct('penilai_id')
            ->count('penilai_id');

        return $jumlahSudah >= $jumlahPenilai;
    }

    // ===================== HELPERS - RANKING =====================

    /**
     * Ambil total rank per usulan dalam satu sub event.
     * Total rank = jumlah ranking dari semua penilai (rank kecil = lebih baik).
     */
    private function getRankingAkhir(int $subEventId): \Illuminate\Support\Collection
    {
        return RankingTahap2::query()
            ->join('usulans', 'ranking_tahap2.usulan_id', '=', 'usulans.id')
            ->where('usulans.sub_event_id', $subEventId)
            ->selectRaw('ranking_tahap2.usulan_id, SUM(ranking_tahap2.ranking) as total_rank')
            ->groupBy('ranking_tahap2.usulan_id')
            ->orderBy('total_rank')
            ->get()
            ->keyBy('usulan_id');
    }

    // ===================== HELPERS - DATA USULAN =====================

    private function getUsulanSplit(int $subEventId): array
    {
        $all = Usulan::forSubEvent($subEventId)
            ->submitted()
            ->where('lolos_tahap1', false)
            ->orderBy('inovator')
            ->get()
            ->map(fn($u) => [
                'id'                        => $u->id,
                'inovator'                  => $u->inovator,
                'nama_inovasi'              => $u->nama_inovasi,
                'kategori'                  => $u->kategori,
                'lolos'                     => false,
                'total_nilai'               => 0,
                'nilai'                     => [],
                'semua_penilai_sudah_nilai' => false,
            ])
            ->toArray();

        $ketJenis  = $this->mapKeteranganJenisTahap1($subEventId);
        $formulasi = FormulasiTahap1::where('sub_event_id', $subEventId)->first();
        $bobot = [
            'makalah'   => (int) ($formulasi->nilai_makalah   ?? 0),
            'substansi' => (int) ($formulasi->nilai_substansi ?? 0),
        ];
        $adaBobot = ($bobot['makalah'] + $bobot['substansi']) > 0;

        $penilaiSubEvent = Penilai::where('sub_event_id', $subEventId)->pluck('id')->toArray();
        $jumlahPenilai   = count($penilaiSubEvent);

        $usulanIds = array_column($all, 'id');
        if (!empty($usulanIds) && !empty($penilaiSubEvent)) {
            $nilaiRows = PenilaianUsulan::whereIn('usulan_id', $usulanIds)
                ->whereIn('penilai_id', $penilaiSubEvent)
                ->get();

            $grouped = [];
            foreach ($nilaiRows as $row) {
                $jenis = $ketJenis[$row->keterangan_indikator_id] ?? 'substansi';
                $grouped[$row->usulan_id][$row->penilai_id][$jenis][] = $row->nilai;
            }

            foreach ($all as &$n) {
                if (!isset($grouped[$n['id']])) continue;

                $totalAll = [];
                foreach ($grouped[$n['id']] as $penilaiId => $perJenis) {
                    $nilaiPenilai        = $this->hitungNilaiBerbobot($perJenis, $bobot, $adaBobot);
                    $n['nilai'][$penilaiId] = $nilaiPenilai;
                    $totalAll[]          = $nilaiPenilai;
                }
                $n['total_nilai'] = !empty($totalAll) ? array_sum($totalAll) / count($totalAll) : 0;

                $sudah = array_keys($grouped[$n['id']]);
                $n['semua_penilai_sudah_nilai'] = $jumlahPenilai > 0
                    && count(array_intersect($sudah, $penilaiSubEvent)) >= $jumlahPenilai;
            }
            unset($n);
        }

        return [
            'umum'    => array_values(array_filter($all, fn($n) => $n['kategori'] === 'umum')),
            'pelajar' => array_values(array_filter($all, fn($n) => $n['kategori'] === 'pelajar')),
        ];
    }

    private function getUsulanLolosSplit(int $subEventId): array
    {
        $all = Usulan::forSubEvent($subEventId)
            ->submitted()
            ->where('lolos_tahap1', true)
            ->orderBy('inovator')
            ->get()
            ->map(fn($u) => [
                'id'                  => $u->id,
                'inovator'            => $u->inovator,
                'nama_inovasi'        => $u->nama_inovasi,
                'kategori'            => $u->kategori,
                'total_nilai_tahap1'  => 0,   // total rata-rata semua penilai
                'nilai_per_penilai'   => [],   // [penilai_id => nilai_berbobot]
                'total_rank'          => 0,
            ])
            ->toArray();
 
        $penilaiSubEvent = Penilai::where('sub_event_id', $subEventId)->pluck('id')->toArray();
        $jumlahPenilai   = count($penilaiSubEvent);
        $usulanIds       = array_column($all, 'id');
 
        // ── Hitung nilai Tahap 1 per penilai ─────────────────────────────────
        if (!empty($usulanIds) && !empty($penilaiSubEvent)) {
            $ketJenisTahap1  = $this->mapKeteranganJenisTahap1($subEventId);
            $formulasiTahap1 = FormulasiTahap1::where('sub_event_id', $subEventId)->first();
            $bobotTahap1 = [
                'makalah'   => (int) ($formulasiTahap1->nilai_makalah   ?? 0),
                'substansi' => (int) ($formulasiTahap1->nilai_substansi ?? 0),
            ];
            $adaBobotTahap1 = ($bobotTahap1['makalah'] + $bobotTahap1['substansi']) > 0;
 
            $nilaiRows = PenilaianUsulan::whereIn('usulan_id', $usulanIds)
                ->whereIn('penilai_id', $penilaiSubEvent)
                ->get();
 
            // grouped[usulan_id][penilai_id][jenis][] = nilai
            $grouped = [];
            foreach ($nilaiRows as $row) {
                $jenis = $ketJenisTahap1[$row->keterangan_indikator_id] ?? 'substansi';
                $grouped[$row->usulan_id][$row->penilai_id][$jenis][] = $row->nilai;
            }
 
            foreach ($all as &$n) {
                if (!isset($grouped[$n['id']])) continue;
 
                $totalAll = [];
                foreach ($grouped[$n['id']] as $penilaiId => $perJenis) {
                    $nilaiPenilai = $this->hitungNilaiBerbobot($perJenis, $bobotTahap1, $adaBobotTahap1);
                    $n['nilai_per_penilai'][$penilaiId] = $nilaiPenilai;
                    $totalAll[] = $nilaiPenilai;
                }
                $n['total_nilai_tahap1'] = !empty($totalAll)
                    ? array_sum($totalAll) / count($totalAll)
                    : 0;
            }
            unset($n);
        }
 
        // ── Ranking Akhir ─────────────────────────────────────────────────────
        $rankingAkhir = $this->getRankingAkhir($subEventId);
        foreach ($all as &$n) {
            $n['total_rank'] = isset($rankingAkhir[$n['id']])
                ? (int) $rankingAkhir[$n['id']]->total_rank
                : 0;
        }
        unset($n);
 
        return [
            'umum'    => array_values(array_filter($all, fn($n) => $n['kategori'] === 'umum')),
            'pelajar' => array_values(array_filter($all, fn($n) => $n['kategori'] === 'pelajar')),
        ];
    }


    private function buildNominasiData(array $subEvents): array
    {
        $subEventIds = array_column($subEvents, 'id');
        $allUsulan   = Usulan::whereIn('sub_event_id', $subEventIds)
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

    private function hitungNilaiPenilaiLogin(int $usulanId, int $penilaiId, int $subEventId): float
    {
        $ketJenis  = $this->mapKeteranganJenisTahap1($subEventId);
        $formulasi = FormulasiTahap1::where('sub_event_id', $subEventId)->first();
        $bobot = [
            'makalah'   => (int) ($formulasi->nilai_makalah   ?? 0),
            'substansi' => (int) ($formulasi->nilai_substansi ?? 0),
        ];
        $adaBobot = ($bobot['makalah'] + $bobot['substansi']) > 0;

        $rows = PenilaianUsulan::where('usulan_id', $usulanId)
            ->where('penilai_id', $penilaiId)
            ->get();

        $perJenis = [];
        foreach ($rows as $row) {
            $jenis = $ketJenis[$row->keterangan_indikator_id] ?? 'substansi';
            $perJenis[$jenis][] = $row->nilai;
        }

        return empty($perJenis) ? 0 : $this->hitungNilaiBerbobot($perJenis, $bobot, $adaBobot);
    }

    private function hitungTotalNilaiTahap1(int $usulanId, int $subEventId): float
    {
        $ketJenis  = $this->mapKeteranganJenisTahap1($subEventId);
        $formulasi = FormulasiTahap1::where('sub_event_id', $subEventId)->first();
        $bobot = [
            'makalah'   => (int) ($formulasi->nilai_makalah   ?? 0),
            'substansi' => (int) ($formulasi->nilai_substansi ?? 0),
        ];
        $adaBobot = ($bobot['makalah'] + $bobot['substansi']) > 0;

        $penilaiIds = Penilai::where('sub_event_id', $subEventId)->pluck('id')->toArray();
        $rows       = PenilaianUsulan::where('usulan_id', $usulanId)
            ->whereIn('penilai_id', $penilaiIds)
            ->get();

        $grouped = [];
        foreach ($rows as $row) {
            $jenis = $ketJenis[$row->keterangan_indikator_id] ?? 'substansi';
            $grouped[$row->penilai_id][$jenis][] = $row->nilai;
        }

        $totalAll = [];
        foreach ($grouped as $perJenis) {
            $totalAll[] = $this->hitungNilaiBerbobot($perJenis, $bobot, $adaBobot);
        }
        return !empty($totalAll) ? array_sum($totalAll) / count($totalAll) : 0;
    }

    // ===================== TAHAP 1 =====================

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

        $indikators = Indikator::where('sub_event_id', $id)
            ->orderBy('jenis')
            ->orderBy('id')
            ->get()
            ->map(fn($ind) => [
                'id'             => $ind->id,
                'nama_indikator' => $ind->nama_indikator,
                'jenis'          => $ind->jenis,
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

        $penilai      = $this->getPenilaiForSubEvent($id);
        $penilaiLogin = $this->getPenilaiLogin($id);

        $nilaiLoginPerUsulan = [];
        if ($penilaiLogin) {
            $usulanIds = array_merge(
                array_column($nominasiUmum,    'id'),
                array_column($nominasiPelajar, 'id')
            );
            $rows = PenilaianUsulan::where('penilai_id', $penilaiLogin->id)
                ->whereIn('usulan_id', $usulanIds)
                ->get();
            foreach ($rows as $row) {
                $nilaiLoginPerUsulan[$row->usulan_id][$row->keterangan_indikator_id] = $row->nilai;
            }
        }

        $totalKeterangan = array_sum(array_map(fn($ind) => count($ind['keterangans']), $indikators));

        return view('master.penilaian.tahap1.show', [
            'subEvent'              => $seArr,
            'nominasiUmum'          => $nominasiUmum,
            'nominasiPelajar'       => $nominasiPelajar,
            'penilai'               => $penilai,
            'indikators'            => $indikators,
            'penilaiLogin'          => $penilaiLogin,
            'nilaiLoginPerInovator' => $nilaiLoginPerUsulan,
            'totalKeterangan'       => $totalKeterangan,
        ]);
    }

    public function tahap1Simpan(Request $request, int $id)
    {
        $request->validate([
            'kategori' => 'required|in:umum,pelajar',
            'ids'      => 'array',
            'ids.*'    => 'integer',
        ]);

        $kandidatId = Usulan::forSubEvent($id)
            ->submitted()
            ->where('kategori', $request->kategori)
            ->where('lolos_tahap1', false)
            ->pluck('id')
            ->toArray();

        $lolosIds = array_values(array_intersect($kandidatId, $request->ids ?? []));

        $belumLengkap = [];
        foreach ($lolosIds as $usulanId) {
            if (!$this->semuaPenilaiSudahMenilaiTahap1($usulanId, $id)) {
                $usulan         = Usulan::find($usulanId);
                $belumLengkap[] = $usulan?->inovator ?? "Usulan #$usulanId";
            }
        }

        if (!empty($belumLengkap)) {
            return response()->json([
                'success'       => false,
                'message'       => 'Penilaian belum lengkap untuk: ' . implode(', ', $belumLengkap)
                                 . '. Semua penilai harus menilai sebelum usulan bisa diloloskan.',
                'belum_lengkap' => $belumLengkap,
            ], 422);
        }

        if (!empty($lolosIds)) {
            Usulan::whereIn('id', $lolosIds)->update([
                'lolos_tahap1' => true,
                'status'       => Usulan::STATUS_SEDANG_DINILAI,
            ]);
        }

        $tidakLolosIds = array_values(array_diff($kandidatId, $lolosIds));
        if (!empty($tidakLolosIds)) {
            Usulan::whereIn('id', $tidakLolosIds)->update([
                'status' => Usulan::STATUS_SELESAI,
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function tahap1SimpanNilai(Request $request, int $id)
    {
        $penilaiLogin = $this->getPenilaiLogin($id);
        abort_unless($penilaiLogin, 403, 'Anda tidak terdaftar sebagai penilai untuk sub event ini.');

        $request->validate([
            'usulan_id' => 'required|integer|exists:usulans,id',
            'nilai'     => 'required|array',
            'nilai.*'   => 'numeric|min:0|max:100',
        ]);

        $usulan = Usulan::query()
            ->where('id', $request->usulan_id)
            ->where('sub_event_id', $id)
            ->where('lolos_tahap1', false)
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

        $nilaiPenilai = $this->hitungNilaiPenilaiLogin($usulan->id, $penilaiLogin->id, $id);
        $totalNilai   = $this->hitungTotalNilaiTahap1($usulan->id, $id);
        $sudahLengkap = $this->semuaPenilaiSudahMenilaiTahap1($usulan->id, $id);

        Log::info('PENILAIAN TAHAP 1', [
            'penilai'       => Auth::user()->email,
            'usulan_id'     => $request->usulan_id,
            'sub_event_id'  => $id,
            'nilai_penilai' => $nilaiPenilai,
            'total_nilai'   => $totalNilai,
        ]);

        return response()->json([
            'success'       => true,
            'nilai_penilai' => round($nilaiPenilai, 2),
            'total_nilai'   => round($totalNilai,   2),
            'sudah_lengkap' => $sudahLengkap,
        ]);
    }

    // ===================== CATATAN PENILAI =====================

    public function simpanCatatan(Request $request, int $usulanId)
    {
        $usulan       = Usulan::findOrFail($usulanId);
        $penilaiLogin = $this->getPenilaiLogin($usulan->sub_event_id);
        abort_unless($penilaiLogin, 403, 'Anda tidak terdaftar sebagai penilai untuk sub event ini.');

        $request->validate(['catatan' => 'required|string|max:1000']);

        CatatanPenilai::updateOrCreate(
            ['usulan_id' => $usulanId, 'penilai_id' => $penilaiLogin->id],
            ['catatan'   => $request->catatan]
        );

        Log::info('CATATAN PENILAI', ['penilai' => Auth::user()->email, 'usulan_id' => $usulanId]);

        return response()->json(['success' => true, 'message' => 'Catatan berhasil disimpan.']);
    }

    public function getCatatan(int $usulanId)
    {
        $usulan       = Usulan::findOrFail($usulanId);
        $penilaiLogin = $this->getPenilaiLogin($usulan->sub_event_id);
        abort_unless($penilaiLogin, 403, 'Anda tidak terdaftar sebagai penilai untuk sub event ini.');

        $catatan = CatatanPenilai::query()
            ->where('usulan_id', $usulanId)
            ->where('penilai_id', $penilaiLogin->id)
            ->first();

        return response()->json(['catatan' => $catatan?->catatan ?? '']);
    }

    // ===================== TAHAP 2 =====================

    public function tahap2()
    {
        $subEvents = $this->getSubEvents();
 
        // Hitung progress ranking (berapa usulan sudah punya minimal 1 ranking)
        $subEventIds = array_column($subEvents, 'id');
        $allUsulan   = Usulan::whereIn('sub_event_id', $subEventIds)
            ->submitted()
            ->where('lolos_tahap1', true)
            ->orderBy('inovator')
            ->get();
 
        $rankingCounts = RankingTahap2::query()
            ->join('usulans', 'ranking_tahap2.usulan_id', '=', 'usulans.id')
            ->whereIn('usulans.sub_event_id', $subEventIds)
            ->selectRaw('usulans.sub_event_id, ranking_tahap2.usulan_id')
            ->distinct()
            ->get()
            ->groupBy('sub_event_id')
            ->map(fn($rows) => $rows->count());
 
        $nominasiData = [];
        foreach ($subEvents as $se) {
            $seId = $se['id'];
            $nominasiData[$seId] = $allUsulan
                ->where('sub_event_id', $seId)
                ->map(fn($u) => [
                    'id'           => $u->id,
                    'inovator'     => $u->inovator,
                    'nama_inovasi' => $u->nama_inovasi,
                    'kategori'     => $u->kategori,
                    // Untuk progress card: sudah ada ranking atau belum
                    'sudah_ranking' => false,  // di-update di bawah
                ])
                ->values()
                ->toArray();
 
            $dinilaiCount = $rankingCounts[$seId] ?? 0;
            foreach ($nominasiData[$seId] as &$n) {
                // tandai jika usulan ini sudah punya ranking
                $n['sudah_ranking'] = $dinilaiCount > 0;
            }
            unset($n);
        }
 
        return view('master.penilaian.tahap2.index', compact('subEvents', 'nominasiData'));
    }


    public function tahap2Show(int $id)
    {
        $subEvent = SubEvent::findOrFail($id);
        $seArr = [
            'id'        => $subEvent->id,
            'sub_event' => $subEvent->sub_event,
            'tahun'     => $subEvent->tahun,
        ];
 
        ['umum' => $nominasiUmum, 'pelajar' => $nominasiPelajar] = $this->getUsulanLolosSplit($id);
 
        $penilai      = $this->getPenilaiForSubEvent($id);
        $penilaiLogin = $this->getPenilaiLogin($id);
 
        // Ranking yang sudah diisi oleh penilai yang login (pre-populate input)
        $rankingLogin = [];
        if ($penilaiLogin) {
            $usulanIds = array_merge(
                array_column($nominasiUmum,    'id'),
                array_column($nominasiPelajar, 'id')
            );
            $rows = RankingTahap2::where('penilai_id', $penilaiLogin->id)
                ->whereIn('usulan_id', $usulanIds)
                ->get();
            foreach ($rows as $row) {
                $rankingLogin[$row->usulan_id] = $row->ranking;
            }
        }
 
        return view('master.penilaian.tahap2.show', [
            'subEvent'      => $seArr,
            'nominasiUmum'  => $nominasiUmum,
            'nominasiPelajar' => $nominasiPelajar,
            'penilai'       => $penilai,
            'penilaiLogin'  => $penilaiLogin,
            'rankingLogin'  => $rankingLogin,
        ]);
    }


    public function tahap2Simpan(Request $request, int $id)
    {
        $penilaiLogin = $this->getPenilaiLogin($id);
        abort_unless($penilaiLogin, 403, 'Anda tidak terdaftar sebagai penilai untuk sub event ini.');

        $request->validate([
            'usulan_id' => 'required|integer|exists:usulans,id',
            'nilai'     => 'required|array',
            'nilai.*'   => 'numeric|min:0|max:100',
        ]);

        $usulan = Usulan::query()
            ->where('id', $request->usulan_id)
            ->where('sub_event_id', $id)
            ->where('lolos_tahap1', true)
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

        $ketJenis  = $this->mapKeteranganJenisTahap2($id);
        $formulasi = FormulasiTahap2::where('sub_event_id', $id)->first();
        $bobot = [
            'Subtansi Inovasi' => (int) ($formulasi->nilai_inovasi  ?? 0),
            'Peragaan'         => (int) ($formulasi->nilai_peragaan ?? 0),
        ];
        $adaBobot = ($bobot['Subtansi Inovasi'] + $bobot['Peragaan']) > 0;

        $rows = Pemenang::where('usulan_id', $usulan->id)
            ->where('penilai_id', $penilaiLogin->id)
            ->get();

        $perJenis = [];
        foreach ($rows as $row) {
            $jenis = $ketJenis[$row->keterangan_tahap2_id] ?? 'Subtansi Inovasi';
            $perJenis[$jenis][] = $row->nilai;
        }
        $nilaiPenilai = empty($perJenis) ? 0 : $this->hitungNilaiBerbobot($perJenis, $bobot, $adaBobot);

        $penilaiIds = Penilai::where('sub_event_id', $id)->pluck('id')->toArray();
        $allRows    = Pemenang::where('usulan_id', $usulan->id)
            ->whereIn('penilai_id', $penilaiIds)
            ->get();

        $grouped = [];
        foreach ($allRows as $row) {
            $jenis = $ketJenis[$row->keterangan_tahap2_id] ?? 'Subtansi Inovasi';
            $grouped[$row->penilai_id][$jenis][] = $row->nilai;
        }

        $totalAll = [];
        foreach ($grouped as $perJenisPenilai) {
            $totalAll[] = $this->hitungNilaiBerbobot($perJenisPenilai, $bobot, $adaBobot);
        }
        $totalNilai = !empty($totalAll) ? array_sum($totalAll) / count($totalAll) : 0;

        $sudahLengkap = $this->semuaPenilaiSudahMenilaiTahap2($usulan->id, $id);

        Log::info('PENILAIAN TAHAP 2', [
            'penilai'       => Auth::user()->email,
            'usulan_id'     => $request->usulan_id,
            'sub_event_id'  => $id,
            'nilai_penilai' => $nilaiPenilai,
            'total_nilai'   => $totalNilai,
        ]);

        return response()->json([
            'success'       => true,
            'nilai_penilai' => round($nilaiPenilai, 2),
            'total_nilai'   => round($totalNilai,   2),
            'sudah_lengkap' => $sudahLengkap,
        ]);
    }

    public function tahap2SimpanRanking(Request $request, int $id)
    {
        $penilaiLogin = $this->getPenilaiLogin($id);
        abort_unless($penilaiLogin, 403, 'Anda tidak terdaftar sebagai penilai.');

        $request->validate([
            'ranking'   => 'required|array',
            'ranking.*' => 'required|integer|min:1',
        ]);

        foreach ($request->ranking as $usulanId => $ranking) {
            // Pastikan usulan memang milik sub event ini dan lolos Tahap 1
            $exists = Usulan::where('id', $usulanId)
                ->where('sub_event_id', $id)
                ->where('lolos_tahap1', true)
                ->exists();

            if (!$exists) continue;

            RankingTahap2::updateOrCreate(
                [
                    'usulan_id'  => (int) $usulanId,
                    'penilai_id' => $penilaiLogin->id,
                ],
                ['ranking' => (int) $ranking]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Ranking berhasil disimpan.',
        ]);
    }
}