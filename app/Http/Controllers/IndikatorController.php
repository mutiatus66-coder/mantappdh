<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubEvent;
use App\Models\Indikator;
use App\Models\KeteranganIndikator;

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
    // TAHAP 2
    // ─────────────────────────────────────────
    public function tahap2()
    {
        $subEvents = SubEvent::orderBy('tahun', 'desc')->get();
        return view('indikator.tahap-2', compact('subEvents'));
    }
}