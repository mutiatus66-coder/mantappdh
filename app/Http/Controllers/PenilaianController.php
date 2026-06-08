<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubEvent;
use App\Models\Penilai;
use App\Models\Usulan;

class PenilaianController extends Controller
{
    // ── Helper: build penilai dengan nama_singkat ─────────────────────────
    private function getPenilai(): array
    {
        return Penilai::all()->map(function ($p) {
            $parts = explode(' ', trim($p->nama));
            $p->nama_singkat = $parts[0]; // ambil kata pertama
            return $p->toArray();
        })->toArray();
    }

    // ── Helper: build nominasi dari usulans ───────────────────────────────
    private function buildNominasiData($subEvents): array
    {
        $nominasiData = [];
        foreach ($subEvents as $se) {
            $id = is_array($se) ? $se['id'] : $se->id;
            $nominasiData[$id] = Usulan::where('sub_event_id', $id)->get()->toArray();
        }
        return $nominasiData;
    }

    // ── Helper: split nominasi per kategori ──────────────────────────────
    private function getNominasiSplit(int $subEventId, bool $lolosOnly = false): array
    {
        $query = Usulan::where('sub_event_id', $subEventId);

        if ($lolosOnly) {
            $query->where('status', 'Lolos Tahap 1');
        }

        $all = $query->get();

        // Build nilai per penilai (kosong dulu, bisa diisi dari tabel nilai nanti)
        $penilai = Penilai::all();

        $mapNominasiRow = function ($u) use ($penilai) {
            $nilai = [];
            foreach ($penilai as $p) {
                $nilai[$p->id] = null; // nanti diisi dari tabel penilaian
            }
            return [
                'id'          => $u->id,
                'inovator'    => $u->inovator,
                'nama_inovasi'=> $u->nama_inovasi,
                'kategori'    => $u->kategori ?? 'umum',
                'total_nilai' => 0,
                'nilai'       => $nilai,
            ];
        };

        $umum    = $all->where('kategori', 'umum')->values()
                       ->map($mapNominasiRow)->toArray();
        $pelajar = $all->where('kategori', 'pelajar')->values()
                       ->map($mapNominasiRow)->toArray();

        return ['umum' => $umum, 'pelajar' => $pelajar];
    }

    // ══════════════════════════════════════════════════════════
    // TAHAP 1 — ambil dari sub_events + semua usulans
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

        ['umum' => $nominasiUmum, 'pelajar' => $nominasiPelajar]
            = $this->getNominasiSplit($id, false);

        usort($nominasiUmum,    fn($a, $b) => strcmp($a['inovator'], $b['inovator']));
        usort($nominasiPelajar, fn($a, $b) => strcmp($a['inovator'], $b['inovator']));

        return view('master.penilaian.tahap1.show', [
            'subEvent'        => $subEvent->toArray(),
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

        // Tandai usulans yang lolos tahap 1
        $lolosIds = $request->ids ?? [];

        // Reset dulu semua yang sebelumnya lolos di sub event + kategori ini
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

    // ══════════════════════════════════════════════════════════
    // TAHAP 2 — hanya usulans yang lolos tahap 1
    // ══════════════════════════════════════════════════════════
    public function tahap2()
    {
        $subEvents = SubEvent::orderBy('tahun', 'desc')->get();

        // Hanya hitung yang lolos tahap 1
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

        // Hanya yang lolos tahap 1
        ['umum' => $nominasiUmum, 'pelajar' => $nominasiPelajar]
            = $this->getNominasiSplit($id, true);

        return view('master.penilaian.tahap2.show', [
            'subEvent'        => $subEvent->toArray(),
            'nominasiUmum'    => $nominasiUmum,
            'nominasiPelajar' => $nominasiPelajar,
            'penilai'         => $this->getPenilai(),
        ]);
    }
}   