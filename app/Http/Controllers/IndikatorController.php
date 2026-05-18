<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubEvent;
use App\Models\Indikator;
use App\Models\KeteranganIndikator;
use App\Models\FormulasiTahap2;

class IndikatorController extends Controller
{
    // ─────────────────────────────────────────
    // TAHAP 1 — Halaman utama daftar Sub Event
    // ─────────────────────────────────────────
    public function tahap1()
    {
        $subEvents = SubEvent::orderBy('tahun', 'desc')->get();
        return view('indikator.tahap-1', compact('subEvents'));
    }

    // ─────────────────────────────────────────
    // DETAIL INOVASI — Daftar indikator per sub event
    // ─────────────────────────────────────────
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

    // ─────────────────────────────────────────
    // DETAIL INDIKATOR — Keterangan + nilai min/maks
    // ─────────────────────────────────────────
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

    // ─────────────────────────────────────────
    // TAHAP 2 — Halaman utama
    // ─────────────────────────────────────────
    public function tahap2()
    {
        $subEvents  = SubEvent::orderBy('tahun', 'desc')->get();
        $formulasis = FormulasiTahap2::pluck('sub_event_id')->toArray();

        return view('indikator.tahap-2', compact('subEvents', 'formulasis'));
    }

    // ─────────────────────────────────────────
    // FORMULASI TAHAP 2 — Simpan / Update
    // ─────────────────────────────────────────
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

    // ─────────────────────────────────────────
    // FORMULASI TAHAP 2 — Get JSON (untuk modal)
    // ─────────────────────────────────────────
    public function formulasiTahap2Get($subEventId)
    {
        $formulasi = FormulasiTahap2::where('sub_event_id', $subEventId)->first();
        if (!$formulasi) {
            return response()->json(['nilai_inovasi' => 0, 'nilai_peragaan' => 0]);
        }
        return response()->json($formulasi);
    }

    // ─────────────────────────────────────────
    // DETAIL INDIKATOR TAHAP 2
    // ─────────────────────────────────────────
    public function detailIndikator2($subEventId)
    {
        $subEvent = SubEvent::findOrFail($subEventId);
        return view('indikator.tahap-2-detail', compact('subEvent'));
    }
}