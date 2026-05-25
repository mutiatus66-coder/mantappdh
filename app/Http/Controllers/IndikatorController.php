<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndikatorController extends Controller
{
    // ══════════════════════════════════════════════════════════
    // DATA MASTER — semua disimpan di session
    // ══════════════════════════════════════════════════════════

    public static function getSubEvents(): array
    {
        return SubEventController::getData();
    }

    public static function getIndikators(): array
    {
        return session('indikators', []);
    }

    public static function getKeterangans(): array
    {
        return session('keterangans', []);
    }

    public static function getIndikatorsTahap2(): array
    {
        return session('indikators_tahap2', []);
    }

    public static function getKeterangansTahap2(): array
    {
        return session('keterangans_tahap2', []);
    }

    public static function getFormulasiTahap1(): array
    {
        return session('formulasi_tahap1', []);
    }

    public static function getFormulasiTahap2(): array
    {
        return session('formulasi_tahap2', []);
    }

    // ══════════════════════════════════════════════════════════
    // TAHAP 1 — Halaman utama
    // ══════════════════════════════════════════════════════════
    public function tahap1()
    {
        $subEvents    = self::getSubEvents();
        $formulasis1  = array_column(self::getFormulasiTahap1(), 'sub_event_id');
        $detailValid1 = [];

        foreach ($subEvents as $se) {
            $f = collect(self::getFormulasiTahap1())->firstWhere('sub_event_id', $se['id']);
            if ($f) {
                $total = ($f['nilai_makalah'] ?? 0) + ($f['nilai_substansi'] ?? 0);
                $detailValid1[$se['id']] = ($total == 100);
            } else {
                $detailValid1[$se['id']] = false;
            }
        }

        return view('indikator.tahap-1', compact('subEvents', 'formulasis1', 'detailValid1'));
    }

    // ── Formulasi Tahap 1 ─────────────────────────────────────
    public function formulasiTahap1Store(Request $request, $subEventId)
    {
        $request->validate([
            'nilai_makalah'   => 'required|integer|min:1|max:100',
            'nilai_substansi' => 'required|integer|min:1|max:100',
        ]);

        if (($request->nilai_makalah + $request->nilai_substansi) !== 100) {
            return back()->withErrors(['total' => 'Total harus 100%.'])->withInput();
        }

        $data = self::getFormulasiTahap1();
        $found = false;
        foreach ($data as &$row) {
            if ($row['sub_event_id'] == $subEventId) {
                $row['nilai_makalah']   = $request->nilai_makalah;
                $row['nilai_substansi'] = $request->nilai_substansi;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $data[] = [
                'id'              => count($data) + 1,
                'sub_event_id'    => (int) $subEventId,
                'nilai_makalah'   => $request->nilai_makalah,
                'nilai_substansi' => $request->nilai_substansi,
            ];
        }

        session(['formulasi_tahap1' => $data]);
        return redirect()->route('indikator.tahap1')->with('success', 'Formulasi Tahap 1 berhasil disimpan.');
    }

    public function formulasiTahap1Get($subEventId)
    {
        $f = collect(self::getFormulasiTahap1())->firstWhere('sub_event_id', (int) $subEventId);
        return response()->json($f ?? ['nilai_makalah' => 0, 'nilai_substansi' => 0]);
    }

    // ══════════════════════════════════════════════════════════
    // TAHAP 1 — Detail Inovasi (list indikator per sub event)
    // ══════════════════════════════════════════════════════════
    public function detailInovasi($subEventId)
    {
        $subEvents    = self::getSubEvents();
        $subEvent     = collect($subEvents)->firstWhere('id', (int) $subEventId);
        $subEventName = $subEvent['sub_event'] ?? '-';
        $indikators   = collect(self::getIndikators())
                            ->where('sub_event_id', (int) $subEventId)
                            ->values()->all();

        return view('indikator.detail_inovasi', compact('subEventId', 'subEventName', 'indikators'));
    }

    public function inovasiStore(Request $request, $subEventId)
    {
        $request->validate(['nama_indikator' => 'required|string|max:255']);

        $data  = self::getIndikators();
        $maxId = count($data) ? max(array_column($data, 'id')) : 0;
        $data[] = [
            'id'             => $maxId + 1,
            'sub_event_id'   => (int) $subEventId,
            'nama_indikator' => $request->nama_indikator,
        ];
        session(['indikators' => $data]);

        return redirect()->route('indikator.tahap1.inovasi', $subEventId)
                         ->with('success', 'Indikator berhasil ditambahkan.');
    }

    public function inovasiUpdate(Request $request, $subEventId, $id)
    {
        $request->validate(['nama_indikator' => 'required|string|max:255']);

        $data = self::getIndikators();
        foreach ($data as &$row) {
            if ($row['id'] == $id && $row['sub_event_id'] == $subEventId) {
                $row['nama_indikator'] = $request->nama_indikator;
                break;
            }
        }
        session(['indikators' => $data]);

        return redirect()->route('indikator.tahap1.inovasi', $subEventId)
                         ->with('success', 'Indikator berhasil diperbarui.');
    }

    public function inovasiDestroy($subEventId, $id)
    {
        $data = array_filter(self::getIndikators(), fn($r) => !($r['id'] == $id && $r['sub_event_id'] == $subEventId));
        session(['indikators' => array_values($data)]);

        return redirect()->route('indikator.tahap1.inovasi', $subEventId)
                         ->with('success', 'Indikator berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════
    // TAHAP 1 — Detail Indikator (keterangan + nilai)
    // ══════════════════════════════════════════════════════════
    public function detailIndikator($subEventId, $indikatorId)
    {
        $indikator     = collect(self::getIndikators())->firstWhere('id', (int) $indikatorId);
        $indikatorName = $indikator['nama_indikator'] ?? '-';
        $keterangans   = collect(self::getKeterangans())
                            ->where('indikator_id', (int) $indikatorId)
                            ->values()->all();

        return view('indikator.detail_indikator', compact('subEventId', 'indikatorId', 'indikatorName', 'keterangans'));
    }

    public function detailIndikatorStore(Request $request, $subEventId, $indikatorId)
    {
        $request->validate([
            'keterangan'     => 'required|string|max:255',
            'nilai_minimal'  => 'required|integer|min:0',
            'nilai_maksimal' => 'required|integer|min:0|gte:nilai_minimal',
        ]);

        $data  = self::getKeterangans();
        $maxId = count($data) ? max(array_column($data, 'id')) : 0;
        $data[] = [
            'id'            => $maxId + 1,
            'indikator_id'  => (int) $indikatorId,
            'keterangan'    => $request->keterangan,
            'nilai_minimal' => $request->nilai_minimal,
            'nilai_maksimal'=> $request->nilai_maksimal,
        ];
        session(['keterangans' => $data]);

        return redirect()->route('indikator.tahap1.detail', [$subEventId, $indikatorId])
                         ->with('success', 'Keterangan berhasil ditambahkan.');
    }

    public function detailIndikatorUpdate(Request $request, $subEventId, $indikatorId, $id)
    {
        $request->validate([
            'keterangan'     => 'required|string|max:255',
            'nilai_minimal'  => 'required|integer|min:0',
            'nilai_maksimal' => 'required|integer|min:0|gte:nilai_minimal',
        ]);

        $data = self::getKeterangans();
        foreach ($data as &$row) {
            if ($row['id'] == $id) {
                $row['keterangan']    = $request->keterangan;
                $row['nilai_minimal'] = $request->nilai_minimal;
                $row['nilai_maksimal']= $request->nilai_maksimal;
                break;
            }
        }
        session(['keterangans' => $data]);

        return redirect()->route('indikator.tahap1.detail', [$subEventId, $indikatorId])
                         ->with('success', 'Keterangan berhasil diperbarui.');
    }

    public function detailIndikatorDestroy($subEventId, $indikatorId, $id)
    {
        $data = array_filter(self::getKeterangans(), fn($r) => $r['id'] != $id);
        session(['keterangans' => array_values($data)]);

        return redirect()->route('indikator.tahap1.detail', [$subEventId, $indikatorId])
                         ->with('success', 'Keterangan berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════
    // TAHAP 2 — Halaman utama
    // ══════════════════════════════════════════════════════════
    public function tahap2()
    {
        $subEvents   = self::getSubEvents();
        $formulasis  = array_column(self::getFormulasiTahap2(), 'sub_event_id');
        $detailValid = [];

        foreach ($subEvents as $se) {
            $f = collect(self::getFormulasiTahap2())->firstWhere('sub_event_id', $se['id']);
            if ($f) {
                $total = ($f['nilai_inovasi'] ?? 0) + ($f['nilai_peragaan'] ?? 0);
                $detailValid[$se['id']] = ($total == 100);
            } else {
                $detailValid[$se['id']] = false;
            }
        }

        return view('indikator.tahap-2', compact('subEvents', 'formulasis', 'detailValid'));
    }

    // ── Formulasi Tahap 2 ─────────────────────────────────────
    public function formulasiTahap2Store(Request $request, $subEventId)
    {
        $request->validate([
            'nilai_inovasi'  => 'required|integer|min:1|max:100',
            'nilai_peragaan' => 'required|integer|min:1|max:100',
        ]);

        if (($request->nilai_inovasi + $request->nilai_peragaan) !== 100) {
            return back()->withErrors(['total' => 'Total harus 100%.'])->withInput();
        }

        $data  = self::getFormulasiTahap2();
        $found = false;
        foreach ($data as &$row) {
            if ($row['sub_event_id'] == $subEventId) {
                $row['nilai_inovasi']  = $request->nilai_inovasi;
                $row['nilai_peragaan'] = $request->nilai_peragaan;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $data[] = [
                'id'             => count($data) + 1,
                'sub_event_id'   => (int) $subEventId,
                'nilai_inovasi'  => $request->nilai_inovasi,
                'nilai_peragaan' => $request->nilai_peragaan,
            ];
        }

        session(['formulasi_tahap2' => $data]);
        return redirect()->route('indikator.tahap2')->with('success', 'Formulasi berhasil disimpan.');
    }

    public function formulasiTahap2Get($subEventId)
    {
        $f = collect(self::getFormulasiTahap2())->firstWhere('sub_event_id', (int) $subEventId);
        return response()->json($f ?? ['nilai_inovasi' => 0, 'nilai_peragaan' => 0]);
    }

    // ══════════════════════════════════════════════════════════
    // TAHAP 2 — Detail Indikator
    // ══════════════════════════════════════════════════════════
    public function detailIndikator2($subEventId)
    {
        $subEvents = self::getSubEvents();
        $subEvent  = collect($subEvents)->firstWhere('id', (int) $subEventId);
        $indikators = collect(self::getIndikatorsTahap2())
                        ->where('sub_event_id', (int) $subEventId)
                        ->values()->all();

        // Gabungkan keterangan ke tiap indikator
        $keterangans = self::getKeterangansTahap2();
        foreach ($indikators as &$ind) {
            $ind['keterangans'] = collect($keterangans)
                ->where('indikator_tahap2_id', $ind['id'])
                ->values()->all();
        }

        return view('indikator.tahap-2-detail', compact('subEvent', 'indikators'));
    }

    public function indikatorTahap2Store(Request $request, $subEventId)
    {
        $request->validate([
            'nama_indikator' => 'required|string|max:255',
            'jenis'          => 'required|in:Subtansi Inovasi,Peragaan',
            'keterangan'     => 'required|string|max:500',
            'nilai_minimal'  => 'required|integer|min:0',
            'nilai_maksimal' => 'required|integer|min:0|gte:nilai_minimal',
        ]);

        $indikators = self::getIndikatorsTahap2();

        // Cari atau buat indikator
        $existing = collect($indikators)->first(fn($r) =>
            $r['sub_event_id'] == $subEventId &&
            $r['nama_indikator'] == $request->nama_indikator &&
            $r['jenis'] == $request->jenis
        );

        if (!$existing) {
            $maxId = count($indikators) ? max(array_column($indikators, 'id')) : 0;
            $existing = [
                'id'             => $maxId + 1,
                'sub_event_id'   => (int) $subEventId,
                'nama_indikator' => $request->nama_indikator,
                'jenis'          => $request->jenis,
            ];
            $indikators[] = $existing;
            session(['indikators_tahap2' => $indikators]);
        }

        $keterangans = self::getKeterangansTahap2();
        $maxKetId    = count($keterangans) ? max(array_column($keterangans, 'id')) : 0;
        $keterangans[] = [
            'id'                  => $maxKetId + 1,
            'indikator_tahap2_id' => $existing['id'],
            'keterangan'          => $request->keterangan,
            'nilai_minimal'       => $request->nilai_minimal,
            'nilai_maksimal'      => $request->nilai_maksimal,
        ];
        session(['keterangans_tahap2' => $keterangans]);

        return redirect()->route('indikator.tahap2.indikator', $subEventId)
                         ->with('success', 'Indikator berhasil ditambahkan.');
    }

    public function indikatorTahap2Update(Request $request, $subEventId, $id)
    {
        $request->validate([
            'nama_indikator' => 'required|string|max:255',
            'jenis'          => 'required|in:Subtansi Inovasi,Peragaan',
            'keterangan'     => 'required|string|max:500',
            'nilai_minimal'  => 'required|integer|min:0',
            'nilai_maksimal' => 'required|integer|min:0|gte:nilai_minimal',
        ]);

        // Update keterangan
        $keterangans = self::getKeterangansTahap2();
        $indikatorId = null;
        foreach ($keterangans as &$ket) {
            if ($ket['id'] == $id) {
                $indikatorId          = $ket['indikator_tahap2_id'];
                $ket['keterangan']    = $request->keterangan;
                $ket['nilai_minimal'] = $request->nilai_minimal;
                $ket['nilai_maksimal']= $request->nilai_maksimal;
                break;
            }
        }
        session(['keterangans_tahap2' => $keterangans]);

        // Update indikator
        if ($indikatorId) {
            $indikators = self::getIndikatorsTahap2();
            foreach ($indikators as &$ind) {
                if ($ind['id'] == $indikatorId) {
                    $ind['nama_indikator'] = $request->nama_indikator;
                    $ind['jenis']          = $request->jenis;
                    break;
                }
            }
            session(['indikators_tahap2' => $indikators]);
        }

        return redirect()->route('indikator.tahap2.indikator', $subEventId)
                         ->with('success', 'Indikator berhasil diperbarui.');
    }

    public function indikatorTahap2Destroy($subEventId, $id)
    {
        $data = array_filter(self::getKeterangansTahap2(), fn($r) => $r['id'] != $id);
        session(['keterangans_tahap2' => array_values($data)]);

        return redirect()->route('indikator.tahap2.indikator', $subEventId)
                         ->with('success', 'Indikator berhasil dihapus.');
    }
}