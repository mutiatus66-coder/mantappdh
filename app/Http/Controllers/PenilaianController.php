<?php

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
    // =========================================================================
    // SECTION 1 — HELPERS UMUM
    // Fungsi pembantu yang digunakan lintas Tahap 1 dan Tahap 2.
    // =========================================================================

    /**
     * Ambil semua SubEvent beserta nama Event induknya, diurutkan tahun terbaru.
     */
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

    /**
     * Ambil daftar penilai untuk satu sub event, lengkap dengan nama singkat.
     */
    private function getPenilaiForSubEvent(int $subEventId): array
    {
        return Penilai::query()->where('sub_event_id', $subEventId)
            ->orderBy('nama')
            ->get()
            ->map(fn($p) => [
                'id'           => $p->id,
                'nama'         => $p->nama,
                'nama_singkat' => $this->namaSingkat($p->nama),
            ])
            ->toArray();
    }

    /**
     * Ekstrak nama depan yang bukan gelar dari nama lengkap.
     * Digunakan untuk header kolom penilai di tabel penilaian.
     *
     * Contoh: "Dr. Budi Santoso, M.T." → "Budi"
     */
    private function namaSingkat(?string $nama): string
    {
        $nama = trim((string) $nama);
        if ($nama === '') return '-';

        $gelar = [
            'dr', 'drs', 'dra', 'ir', 'prof', 'h', 'hj', 'kh',
            'st', 'se', 'mm', 'mt', 'msi', 'spd', 'mpd', 'phd',
        ];

        $parts = preg_split('/\s+/', $nama);
        foreach ($parts as $part) {
            $bersih = strtolower(rtrim($part, '.,'));
            if ($bersih === '' || in_array($bersih, $gelar, true)) {
                continue;
            }
            return $part;
        }

        return $parts[0] ?? $nama;
    }

    /**
     * Ambil data penilai yang sedang login untuk sub event tertentu.
     * Mengembalikan null jika user yang login bukan penilai di sub event ini.
     */
    private function getPenilaiLogin(int $subEventId): ?object
    {
        return Penilai::where('user_id', auth()->id())
            ->where('sub_event_id', $subEventId)
            ->first();
    }

    // =========================================================================
    // SECTION 2 — HELPERS FORMULASI & PERHITUNGAN NILAI
    // Fungsi untuk normalisasi jenis indikator dan penghitungan nilai berbobot.
    // =========================================================================

    /**
     * Normalisasi jenis indikator Tahap 1 ke dua kanon: 'makalah' atau 'substansi'.
     */
    private function canonJenisTahap1(?string $jenis): string
    {
        $j = strtolower(trim((string) $jenis));
        return str_contains($j, 'makalah') ? 'makalah' : 'substansi';
    }

    /**
     * Normalisasi jenis indikator Tahap 2 ke dua kanon: 'Peragaan' atau 'Subtansi Inovasi'.
     */
    private function canonJenisTahap2(?string $jenis): string
    {
        $j = strtolower(trim((string) $jenis));
        return str_contains($j, 'perag') ? 'Peragaan' : 'Subtansi Inovasi';
    }

    /**
     * Buat peta: keterangan_indikator_id → jenis ('makalah' | 'substansi')
     * untuk sub event tertentu. Digunakan saat mengelompokkan nilai per jenis
     * sebelum menghitung bobot.
     */
    private function mapKeteranganJenisTahap1(int $subEventId): array
    {
        $indikators = Indikator::query()->where('sub_event_id', $subEventId)
            ->get(['id', 'jenis']);

        if ($indikators->isEmpty()) return [];

        $indikatorJenis = $indikators->mapWithKeys(fn($ind) => [
            $ind->id => $this->canonJenisTahap1($ind->jenis ?? 'substansi'),
        ])->toArray();

        return KeteranganIndikator::whereIn('indikator_id', array_keys($indikatorJenis))
            ->get(['id', 'indikator_id'])
            ->mapWithKeys(fn($k) => [
                $k->id => $indikatorJenis[$k->indikator_id] ?? 'substansi',
            ])->toArray();
    }

    /**
     * Buat peta: keterangan_tahap2_id → jenis ('Peragaan' | 'Subtansi Inovasi')
     * untuk sub event tertentu.
     */
    private function mapKeteranganJenisTahap2(int $subEventId): array
    {
        $indikators = IndikatorTahap2::query()->where('sub_event_id', $subEventId)
            ->get(['id', 'jenis']);

        if ($indikators->isEmpty()) return [];

        $indikatorJenis = $indikators->mapWithKeys(fn($ind) => [
            $ind->id => $this->canonJenisTahap2($ind->jenis ?? 'Subtansi Inovasi'),
        ])->toArray();

        return KeteranganTahap2::whereIn('indikator_tahap2_id', array_keys($indikatorJenis))
            ->get(['id', 'indikator_tahap2_id'])
            ->mapWithKeys(fn($k) => [
                $k->id => $indikatorJenis[$k->indikator_tahap2_id] ?? 'Subtansi Inovasi',
            ])->toArray();
    }

    /**
     * Hitung nilai akhir satu penilai untuk satu usulan, dengan mempertimbangkan bobot.
     *
     * Alur:
     *   1. Rata-rata nilai per jenis (misal: avg(makalah), avg(substansi))
     *   2. Jika ada formulasi bobot → nilai_berbobot = Σ(avg_jenis × bobot_jenis) / Σbobot
     *   3. Jika tidak ada bobot     → rata-rata semua nilai tanpa pemisahan jenis
     *
     * @param  array $perJenis  [jenis => [nilai, nilai, ...]]
     * @param  array $bobot     [jenis => bobot_persen]
     * @param  bool  $adaBobot  true jika total bobot > 0
     */
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
                if ($bobotNilai <= 0 || !isset($avgPerJenis[$jenis])) continue;
                $totalNilai += $avgPerJenis[$jenis] * $bobotNilai;
                $totalBobot += $bobotNilai;
            }
            return $totalBobot > 0 ? ($totalNilai / $totalBobot) : 0;
        }

        if (empty($perJenis)) return 0;
        $semua = array_merge(...array_values($perJenis));
        return count($semua) > 0 ? array_sum($semua) / count($semua) : 0;
    }

    // =========================================================================
    // SECTION 3 — HELPERS STATUS PENILAIAN
    // Cek apakah semua penilai sudah memberikan nilai untuk satu usulan.
    // =========================================================================

    /**
     * Cek apakah semua penilai sub event sudah menilai usulan di Tahap 1.
     * Digunakan sebagai syarat sebelum usulan bisa diloloskan ke Tahap 2.
     */
    private function semuaPenilaiSudahMenilaiTahap1(int $usulanId, int $subEventId): bool
    {
        $penilaiIds    = Penilai::query()->where('sub_event_id', $subEventId)->pluck('id');
        $jumlahPenilai = $penilaiIds->count();

        if ($jumlahPenilai === 0) return false;

        $jumlahSudah = PenilaianUsulan::query()->where('usulan_id', $usulanId)
            ->whereIn('penilai_id', $penilaiIds)
            ->distinct('penilai_id')
            ->count('penilai_id');

        return $jumlahSudah >= $jumlahPenilai;
    }

    /**
     * Cek apakah semua penilai sub event sudah menilai usulan di Tahap 2.
     */
    private function semuaPenilaiSudahMenilaiTahap2(int $usulanId, int $subEventId): bool
    {
        $penilaiIds    = Penilai::query()->where('sub_event_id', $subEventId)->pluck('id');
        $jumlahPenilai = $penilaiIds->count();

        if ($jumlahPenilai === 0) return false;

        $jumlahSudah = Pemenang::query()->where('usulan_id', $usulanId)
            ->whereIn('penilai_id', $penilaiIds)
            ->distinct('penilai_id')
            ->count('penilai_id');

        return $jumlahSudah >= $jumlahPenilai;
    }

    // =========================================================================
    // SECTION 4 — HELPERS RANKING
    // =========================================================================

    /**
     * Ambil total rank per usulan dalam satu sub event.
     * Total rank = jumlah ranking dari semua penilai.
     * Rank kecil = posisi lebih baik (rank 1 terbaik).
     *
     * @return \Illuminate\Support\Collection  keyed by usulan_id
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
            ->keyBy(fn($row) => (int) $row->usulan_id);
    }

    // =========================================================================
    // SECTION 5 — HELPERS DATA USULAN
    // Fungsi untuk membangun struktur data usulan lengkap dengan nilai.
    // =========================================================================

    /**
     * Ambil usulan Tahap 1 yang BELUM lolos, lengkap dengan nilai per penilai
     * dan status kelengkapan penilaian. Dibagi per kategori (umum / pelajar).
     *
     * Digunakan oleh: tahap1Show()
     */
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
        $formulasi = FormulasiTahap1::query()->where('sub_event_id', $subEventId)->first();
        $bobot = [
            'makalah'   => (int) ($formulasi->nilai_makalah   ?? 0),
            'substansi' => (int) ($formulasi->nilai_substansi ?? 0),
        ];
        $adaBobot = ($bobot['makalah'] + $bobot['substansi']) > 0;

        $penilaiSubEvent = Penilai::query()->where('sub_event_id', $subEventId)->pluck('id')->toArray();
        $jumlahPenilai   = count($penilaiSubEvent);
        $usulanIds       = array_column($all, 'id');

        if (!empty($usulanIds) && !empty($penilaiSubEvent)) {
            $nilaiRows = PenilaianUsulan::whereIn('usulan_id', $usulanIds)
                ->whereIn('penilai_id', $penilaiSubEvent)
                ->get();

            // grouped[usulan_id][penilai_id][jenis][] = nilai
            $grouped = [];
            foreach ($nilaiRows as $row) {
                $jenis = $ketJenis[$row->keterangan_indikator_id] ?? 'substansi';
                $grouped[$row->usulan_id][$row->penilai_id][$jenis][] = $row->nilai;
            }

            foreach ($all as &$n) {
                if (!isset($grouped[$n['id']])) continue;

                $totalAll = [];
                foreach ($grouped[$n['id']] as $penilaiId => $perJenis) {
                    $nilaiPenilai           = $this->hitungNilaiBerbobot($perJenis, $bobot, $adaBobot);
                    $n['nilai'][$penilaiId] = $nilaiPenilai;
                    $totalAll[]             = $nilaiPenilai;
                }
                $n['total_nilai'] = !empty($totalAll)
                    ? array_sum($totalAll) / count($totalAll)
                    : 0;

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

    /**
     * Ambil usulan Tahap 2 yang SUDAH lolos Tahap 1, lengkap dengan nilai
     * Tahap 1 (sebagai referensi) dan total rank Tahap 2.
     * Dibagi per kategori (umum / pelajar).
     *
     * Digunakan oleh: tahap2Show()
     */
    private function getUsulanLolosSplit(int $subEventId): array
    {
        $all = Usulan::forSubEvent($subEventId)
            ->submitted()
            ->where('lolos_tahap1', true)
            ->orderBy('inovator')
            ->get()
            ->map(fn($u) => [
                'id'                 => $u->id,
                'inovator'           => $u->inovator,
                'nama_inovasi'       => $u->nama_inovasi,
                'kategori'           => $u->kategori,
                'total_nilai_tahap1' => 0,
                'nilai_per_penilai'  => [],
                'total_rank'         => 0,
            ])
            ->toArray();

        $penilaiSubEvent = Penilai::query()->where('sub_event_id', $subEventId)->pluck('id')->toArray();
        $usulanIds       = array_column($all, 'id');

        if (!empty($usulanIds) && !empty($penilaiSubEvent)) {
            $ketJenisTahap1  = $this->mapKeteranganJenisTahap1($subEventId);
            $formulasiTahap1 = FormulasiTahap1::query()->where('sub_event_id', $subEventId)->first();
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
                    $nilaiPenilai                       = $this->hitungNilaiBerbobot($perJenis, $bobotTahap1, $adaBobotTahap1);
                    $n['nilai_per_penilai'][$penilaiId] = $nilaiPenilai;
                    $totalAll[]                         = $nilaiPenilai;
                }
                $n['total_nilai_tahap1'] = !empty($totalAll)
                    ? array_sum($totalAll) / count($totalAll)
                    : 0;
            }
            unset($n);
        }

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

    /**
     * Bangun peta nominasiData untuk halaman index Tahap 1:
     *   [sub_event_id => [['id', 'inovator', 'nama_inovasi', 'kategori', 'sudah_dinilai'], ...]]
     *
     * Hanya usulan yang BELUM lolos Tahap 1.
     * 'sudah_dinilai' = true jika minimal 1 penilai sudah mengisi nilai.
     * Digunakan untuk menghitung progress % di card index.
     *
     * FIX: sebelumnya total_nilai selalu 0 (tidak pernah dihitung) sehingga
     * progress selalu 0%. Sekarang pakai flag sudah_dinilai dari 1 query EXISTS.
     */
    private function buildNominasiDataTahap1(array $subEvents): array
    {
        $subEventIds = array_column($subEvents, 'id');

        $allUsulan = Usulan::whereIn('sub_event_id', $subEventIds)
            ->submitted()
            ->where('lolos_tahap1', false)
            ->orderBy('inovator')
            ->get();

        // Usulan yang sudah ada minimal 1 nilai (1 query, O(1) lookup via flip)
        $sudahDinilaiIds = $allUsulan->isNotEmpty()
            ? PenilaianUsulan::whereIn('usulan_id', $allUsulan->pluck('id'))
                ->distinct('usulan_id')
                ->pluck('usulan_id')
                ->flip()
            : collect();

        $nominasiData = [];
        foreach ($subEvents as $se) {
            $nominasiData[$se['id']] = $allUsulan
                ->where('sub_event_id', $se['id'])
                ->map(fn($u) => [
                    'id'            => $u->id,
                    'inovator'      => $u->inovator,
                    'nama_inovasi'  => $u->nama_inovasi,
                    'kategori'      => $u->kategori,
                    'sudah_dinilai' => isset($sudahDinilaiIds[$u->id]),
                ])
                ->values()
                ->toArray();
        }

        return $nominasiData;
    }

    /**
     * Bangun peta nominasiData untuk halaman index Tahap 2:
     *   [sub_event_id => [['id', ..., 'sudah_ranking'], ...]]
     *
     * Hanya usulan yang SUDAH lolos Tahap 1. Progress dihitung per-usulan
     * (sudah punya ranking atau belum), bukan per sub event.
     */
    private function buildNominasiDataTahap2(array $subEvents): array
    {
        $subEventIds = array_column($subEvents, 'id');

        $allUsulan = Usulan::whereIn('sub_event_id', $subEventIds)
            ->submitted()
            ->where('lolos_tahap1', true)
            ->orderBy('inovator')
            ->get();

        // Ambil usulan_id yang sudah punya minimal 1 ranking (1 query, O(1) lookup)
        $usulanSudahRanking = RankingTahap2::query()
            ->join('usulans', 'ranking_tahap2.usulan_id', '=', 'usulans.id')
            ->whereIn('usulans.sub_event_id', $subEventIds)
            ->distinct()
            ->pluck('ranking_tahap2.usulan_id')
            ->flip();

        $nominasiData = [];
        foreach ($subEvents as $se) {
            $nominasiData[$se['id']] = $allUsulan
                ->where('sub_event_id', $se['id'])
                ->map(fn($u) => [
                    'id'            => $u->id,
                    'inovator'      => $u->inovator,
                    'nama_inovasi'  => $u->nama_inovasi,
                    'kategori'      => $u->kategori,
                    'sudah_ranking' => isset($usulanSudahRanking[$u->id]),
                ])
                ->values()
                ->toArray();
        }

        return $nominasiData;
    }

    /**
     * Hitung nilai berbobot penilai yang sedang login untuk satu usulan.
     * Digunakan sebagai return value setelah simpan nilai (live update tabel).
     */
    private function hitungNilaiPenilaiLogin(int $usulanId, int $penilaiId, int $subEventId): float
    {
        $ketJenis  = $this->mapKeteranganJenisTahap1($subEventId);
        $formulasi = FormulasiTahap1::query()->where('sub_event_id', $subEventId)->first();
        $bobot = [
            'makalah'   => (int) ($formulasi->nilai_makalah   ?? 0),
            'substansi' => (int) ($formulasi->nilai_substansi ?? 0),
        ];
        $adaBobot = ($bobot['makalah'] + $bobot['substansi']) > 0;

        $rows = PenilaianUsulan::query()->where('usulan_id', $usulanId)
            ->where('penilai_id', $penilaiId)
            ->get();

        $perJenis = [];
        foreach ($rows as $row) {
            $jenis = $ketJenis[$row->keterangan_indikator_id] ?? 'substansi';
            $perJenis[$jenis][] = $row->nilai;
        }

        return empty($perJenis) ? 0 : $this->hitungNilaiBerbobot($perJenis, $bobot, $adaBobot);
    }

    /**
     * Hitung total nilai Tahap 1 (rata-rata semua penilai) untuk satu usulan.
     * Digunakan sebagai return value setelah simpan nilai (live update tabel).
     */
    private function hitungTotalNilaiTahap1(int $usulanId, int $subEventId): float
    {
        $ketJenis  = $this->mapKeteranganJenisTahap1($subEventId);
        $formulasi = FormulasiTahap1::query()->where('sub_event_id', $subEventId)->first();
        $bobot = [
            'makalah'   => (int) ($formulasi->nilai_makalah   ?? 0),
            'substansi' => (int) ($formulasi->nilai_substansi ?? 0),
        ];
        $adaBobot = ($bobot['makalah'] + $bobot['substansi']) > 0;

        $penilaiIds = Penilai::query()->where('sub_event_id', $subEventId)->pluck('id')->toArray();
        $rows       = PenilaianUsulan::query()->where('usulan_id', $usulanId)
            ->whereIn('penilai_id', $penilaiIds)
            ->get();

        // grouped[penilai_id][jenis][] = nilai
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

    // =========================================================================
    // SECTION 6 — TAHAP 1 (Controllers)
    // =========================================================================

    /**
     * GET /penilaian/tahap1
     * Halaman index Tahap 1 — daftar card per sub event dengan progress penilaian.
     */
    public function tahap1()
    {
        $subEvents    = $this->getSubEvents();
        $nominasiData = $this->buildNominasiDataTahap1($subEvents);

        return view('master.penilaian.tahap1.index', compact('subEvents', 'nominasiData'));
    }

    /**
     * GET /penilaian/tahap1/{id}
     * Halaman detail Tahap 1 — tabel penilaian per usulan untuk satu sub event.
     */
    public function tahap1Show(int $id)
    {
        $subEvent = SubEvent::findOrFail($id);
        $seArr    = [
            'id'        => $subEvent->id,
            'sub_event' => $subEvent->sub_event,
            'tahun'     => $subEvent->tahun,
        ];

        ['umum' => $nominasiUmum, 'pelajar' => $nominasiPelajar] = $this->getUsulanSplit($id);

        $indikators = Indikator::query()->where('sub_event_id', $id)
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

        // Nilai yang sudah diisi penilai login (untuk pre-populate modal)
        // [usulan_id => [keterangan_indikator_id => nilai]]
        $nilaiLoginPerUsulan = [];
        if ($penilaiLogin) {
            $usulanIds = array_merge(
                array_column($nominasiUmum,    'id'),
                array_column($nominasiPelajar, 'id')
            );
            $rows = PenilaianUsulan::query()->where('penilai_id', $penilaiLogin->id)
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

    /**
     * POST /penilaian/tahap1/{id}/simpan
     * Simpan keputusan lolos/tidak lolos setelah penilaian selesai.
     *
     * Request:
     *   - kategori : 'umum' | 'pelajar'
     *   - ids[]    : array usulan_id yang diloloskan (boleh kosong)
     *
     * Validasi: semua penilai wajib sudah menilai sebelum usulan bisa diloloskan.
     */
    public function tahap1Simpan(Request $request, int $id)
    {
        $validated = $request->validate([
        'kategori' => 'required|in:umum,pelajar',
        'ids'      => 'array',
        'ids.*'    => 'integer',
    ]);

        // ── Validasi maksimal 7 per kategori ──────────────────────────────
        $MAX_LOLOS = 7;
        if (count($validated['ids'] ?? []) > $MAX_LOLOS) {
            return response()->json([
                'success' => false,
                'message' => "Maksimal {$MAX_LOLOS} inovasi yang dapat diloloskan untuk kategori {$validated['kategori']}.",
            ], 422);
        }

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

    /**
     * POST /penilaian/tahap1/{id}/simpan-nilai
     * Simpan nilai indikator dari penilai yang login untuk satu usulan.
     *
     * Request:
     *   - usulan_id : integer
     *   - nilai     : [keterangan_indikator_id => nilai (0–100)]
     *
     * Response:
     *   - nilai_penilai : nilai berbobot penilai ini
     *   - total_nilai   : rata-rata semua penilai
     *   - sudah_lengkap : apakah semua penilai sudah menilai
     */
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

    // =========================================================================
    // SECTION 7 — CATATAN PENILAI
    // =========================================================================

    /**
     * POST /penilaian/catatan/{usulanId}
     * Simpan atau update catatan penilai untuk satu usulan.
     */
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

        Log::info('CATATAN PENILAI', [
            'penilai'   => Auth::user()->email,
            'usulan_id' => $usulanId,
        ]);

        return response()->json(['success' => true, 'message' => 'Catatan berhasil disimpan.']);
    }

    /**
     * GET /penilaian/catatan/{usulanId}
     * Ambil catatan penilai yang sedang login untuk satu usulan.
     */
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

    // =========================================================================
    // SECTION 8 — TAHAP 2 (Controllers)
    // =========================================================================

    /**
     * GET /penilaian/tahap2
     * Halaman index Tahap 2 — daftar card per sub event dengan progress ranking.
     */
    public function tahap2()
    {
        $subEvents    = $this->getSubEvents();
        $nominasiData = $this->buildNominasiDataTahap2($subEvents);

        return view('master.penilaian.tahap2.index', compact('subEvents', 'nominasiData'));
    }

    /**
     * GET /penilaian/tahap2/{id}
     * Halaman detail Tahap 2 — tabel nilai + ranking nominator untuk satu sub event.
     */
    public function tahap2Show(int $id)
    {
        $subEvent = SubEvent::findOrFail($id);
        $seArr    = [
            'id'        => $subEvent->id,
            'sub_event' => $subEvent->sub_event,
            'tahun'     => $subEvent->tahun,
        ];

        ['umum' => $nominasiUmum, 'pelajar' => $nominasiPelajar] = $this->getUsulanLolosSplit($id);

        $penilai      = $this->getPenilaiForSubEvent($id);
        $penilaiLogin = $this->getPenilaiLogin($id);

        // Ranking yang sudah diisi penilai login (untuk pre-populate input ranking)
        // [usulan_id => ranking]
        $rankingLogin = [];
        if ($penilaiLogin) {
            $usulanIds = array_merge(
                array_column($nominasiUmum,    'id'),
                array_column($nominasiPelajar, 'id')
            );
            $rows = RankingTahap2::query()->where('penilai_id', $penilaiLogin->id)
                ->whereIn('usulan_id', $usulanIds)
                ->get();
            foreach ($rows as $row) {
                $rankingLogin[$row->usulan_id] = $row->ranking;
            }
        }

        return view('master.penilaian.tahap2.show', [
            'subEvent'        => $seArr,
            'nominasiUmum'    => $nominasiUmum,
            'nominasiPelajar' => $nominasiPelajar,
            'penilai'         => $penilai,
            'penilaiLogin'    => $penilaiLogin,
            'rankingLogin'    => $rankingLogin,
        ]);
    }

    /**
     * POST /penilaian/tahap2/{id}/simpan
     * Simpan nilai indikator Tahap 2 dari penilai yang login untuk satu usulan.
     *
     * Request:
     *   - usulan_id : integer
     *   - nilai     : [keterangan_tahap2_id => nilai (0–100)]
     *
     * Response:
     *   - nilai_penilai : nilai berbobot penilai ini
     *   - total_nilai   : rata-rata semua penilai
     *   - sudah_lengkap : apakah semua penilai sudah menilai
     */
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
        $formulasi = FormulasiTahap2::query()->where('sub_event_id', $id)->first();
        $bobot = [
            'Subtansi Inovasi' => (int) ($formulasi->nilai_inovasi  ?? 0),
            'Peragaan'         => (int) ($formulasi->nilai_peragaan ?? 0),
        ];
        $adaBobot = ($bobot['Subtansi Inovasi'] + $bobot['Peragaan']) > 0;

        $rows = Pemenang::query()->where('usulan_id', $usulan->id)
            ->where('penilai_id', $penilaiLogin->id)
            ->get();

        $perJenis = [];
        foreach ($rows as $row) {
            $jenis = $ketJenis[$row->keterangan_tahap2_id] ?? 'Subtansi Inovasi';
            $perJenis[$jenis][] = $row->nilai;
        }
        $nilaiPenilai = empty($perJenis) ? 0 : $this->hitungNilaiBerbobot($perJenis, $bobot, $adaBobot);

        $penilaiIds = Penilai::query()->where('sub_event_id', $id)->pluck('id')->toArray();
        $allRows    = Pemenang::query()->where('usulan_id', $usulan->id)
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

    /**
     * POST /penilaian/tahap2/{id}/simpan-ranking
     * Simpan atau update ranking Tahap 2 dari penilai yang login.
     *
     * Request:
     *   - ranking : [usulan_id => ranking (integer ≥ 1)]
     */
    public function tahap2SimpanRanking(Request $request, int $id)
    {
        $penilaiLogin = $this->getPenilaiLogin($id);
        abort_unless($penilaiLogin, 403, 'Anda tidak terdaftar sebagai penilai.');

        $request->validate([
            'ranking'   => 'required|array',
            'ranking.*' => 'required|integer|min:1',
        ]);

        foreach ($request->ranking as $usulanId => $ranking) {
            $exists = Usulan::query()->where('id', $usulanId)
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