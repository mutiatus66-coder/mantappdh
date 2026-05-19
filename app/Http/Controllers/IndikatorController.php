<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubEvent;
use App\Models\Indikator;
use App\Models\KeteranganIndikator;
use App\Models\FormulasiTahap2;
use App\Models\IndikatorTahap2;
use App\Models\KeteranganTahap2;

class IndikatorController extends Controller
{
    // ── TAHAP 1 ──────────────────────────────────────────────
    public function tahap1()
    {
        $subEvents = SubEvent::orderBy('tahun', 'desc')->get();
        return view('indikator.tahap-1', compact('subEvents'));
    }

    public function detailInovasi($subEventId)
    {
        $subEvent     = SubEvent::findOrFail($subEventId);
        $subEventName = $subEvent->sub_event;
        $indikators   = Indikator::where('sub_event_id', $subEventId)->get();
        return view('indikator.detail_inovasi', compact('subEventId', 'subEventName', 'indikators'));
    }

    public function inovasiStore(Request $request, $subEventId)
    {
        $request->validate(['nama_indikator' => 'required|string|max:255']);
        SubEvent::findOrFail($subEventId);
        Indikator::create([
            'sub_event_id'   => $subEventId,
            'nama_indikator' => $request->nama_indikator,
        ]);
        return redirect()->route('indikator.tahap1.inovasi', $subEventId)
                         ->with('success', 'Indikator berhasil ditambahkan.');
    }

    public function inovasiUpdate(Request $request, $subEventId, $id)
    {
        $request->validate(['nama_indikator' => 'required|string|max:255']);
        $indikator = Indikator::where('sub_event_id', $subEventId)->findOrFail($id);
        $indikator->update(['nama_indikator' => $request->nama_indikator]);
        return redirect()->route('indikator.tahap1.inovasi', $subEventId)
                         ->with('success', 'Indikator berhasil diperbarui.');
    }

    public function inovasiDestroy($subEventId, $id)
    {
        Indikator::where('sub_event_id', $subEventId)->findOrFail($id)->delete();
        return redirect()->route('indikator.tahap1.inovasi', $subEventId)
                         ->with('success', 'Indikator berhasil dihapus.');
    }

    public function detailIndikator($subEventId, $indikatorId)
    {
        SubEvent::findOrFail($subEventId);
        $indikator     = Indikator::findOrFail($indikatorId);
        $indikatorName = $indikator->nama_indikator;
        $keterangans   = KeteranganIndikator::where('indikator_id', $indikatorId)->get();
        return view('indikator.detail_indikator', compact(
            'subEventId', 'indikatorId', 'indikatorName', 'keterangans'
        ));
    }

    public function detailIndikatorStore(Request $request, $subEventId, $indikatorId)
    {
        $request->validate([
            'keterangan'     => 'required|string|max:255',
            'nilai_minimal'  => 'required|integer|min:0',
            'nilai_maksimal' => 'required|integer|min:0|gte:nilai_minimal',
        ]);
        Indikator::findOrFail($indikatorId);
        KeteranganIndikator::create([
            'indikator_id'   => $indikatorId,
            'keterangan'     => $request->keterangan,
            'nilai_minimal'  => $request->nilai_minimal,
            'nilai_maksimal' => $request->nilai_maksimal,
        ]);
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
        $item = KeteranganIndikator::where('indikator_id', $indikatorId)->findOrFail($id);
        $item->update([
            'keterangan'     => $request->keterangan,
            'nilai_minimal'  => $request->nilai_minimal,
            'nilai_maksimal' => $request->nilai_maksimal,
        ]);
        return redirect()->route('indikator.tahap1.detail', [$subEventId, $indikatorId])
                         ->with('success', 'Keterangan berhasil diperbarui.');
    }

    public function detailIndikatorDestroy($subEventId, $indikatorId, $id)
    {
        KeteranganIndikator::where('indikator_id', $indikatorId)->findOrFail($id)->delete();
        return redirect()->route('indikator.tahap1.detail', [$subEventId, $indikatorId])
                         ->with('success', 'Keterangan berhasil dihapus.');
    }

    // ── TAHAP 2 — Halaman utama ───────────────────────────────
    public function tahap2()
    {
        $subEvents  = SubEvent::orderBy('tahun', 'desc')->get();
        $formulasis = FormulasiTahap2::pluck('sub_event_id')->toArray();
        $detailValid = [];
        foreach ($subEvents as $subEvent) {
            $formulasi = FormulasiTahap2::where('sub_event_id', $subEvent->id)->first();
            if ($formulasi) {
                $total = ($formulasi->nilai_inovasi ?? 0) + ($formulasi->nilai_peragaan ?? 0);
                $detailValid[$subEvent->id] = ($total == 100);
            } else {
                $detailValid[$subEvent->id] = false;
            }
        }
        return view('indikator.tahap-2', compact('subEvents', 'formulasis', 'detailValid'));
    }

    // ── TAHAP 2 — Detail Indikator ────────────────────────────
    public function detailIndikator2($subEventId)
    {
        $subEvent   = SubEvent::findOrFail($subEventId);
        $indikators = IndikatorTahap2::with('keterangans')
                        ->where('sub_event_id', $subEventId)
                        ->get();
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

        SubEvent::findOrFail($subEventId);

        // Cari atau buat indikator dengan nama + jenis yang sama
        $indikator = IndikatorTahap2::firstOrCreate(
            [
                'sub_event_id'   => $subEventId,
                'nama_indikator' => $request->nama_indikator,
                'jenis'          => $request->jenis,
            ]
        );

        KeteranganTahap2::create([
            'indikator_tahap2_id' => $indikator->id,
            'keterangan'          => $request->keterangan,
            'nilai_minimal'       => $request->nilai_minimal,
            'nilai_maksimal'      => $request->nilai_maksimal,
        ]);

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

        $ket = KeteranganTahap2::findOrFail($id);

        // Update nama & jenis di tabel indikator
        $ket->indikator->update([
            'nama_indikator' => $request->nama_indikator,
            'jenis'          => $request->jenis,
        ]);

        // Update keterangan
        $ket->update([
            'keterangan'     => $request->keterangan,
            'nilai_minimal'  => $request->nilai_minimal,
            'nilai_maksimal' => $request->nilai_maksimal,
        ]);

        return redirect()->route('indikator.tahap2.indikator', $subEventId)
                         ->with('success', 'Indikator berhasil diperbarui.');
    }

    public function indikatorTahap2Destroy($subEventId, $id)
    {
        KeteranganTahap2::findOrFail($id)->delete();
        return redirect()->route('indikator.tahap2.indikator', $subEventId)
                         ->with('success', 'Indikator berhasil dihapus.');
    }

    // ── TAHAP 2 — Formulasi ───────────────────────────────────
    public function formulasiTahap2Store(Request $request, $subEventId)
    {
        $request->validate([
            'nilai_inovasi'  => 'required|integer|min:1|max:100',
            'nilai_peragaan' => 'required|integer|min:1|max:100',
        ]);

        if (($request->nilai_inovasi + $request->nilai_peragaan) !== 100) {
            return back()->withErrors([
                'total' => 'Total Nilai Inovasi dan Nilai Peragaan harus 100%.'
            ])->withInput();
        }

        SubEvent::findOrFail($subEventId);

        FormulasiTahap2::updateOrCreate(
            ['sub_event_id' => $subEventId],
            [
                'nilai_inovasi'  => $request->nilai_inovasi,
                'nilai_peragaan' => $request->nilai_peragaan,
            ]
        );

        return redirect()->route('indikator.tahap2')
                         ->with('success', 'Formulasi berhasil disimpan.');
    }

    public function formulasiTahap2Get($subEventId)
    {
        $formulasi = FormulasiTahap2::where('sub_event_id', $subEventId)->first();
        if (!$formulasi) {
            return response()->json(['nilai_inovasi' => 0, 'nilai_peragaan' => 0]);
        }
        return response()->json($formulasi);
    }
}