<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubEvent;
use App\Models\Indikator;
use App\Models\KeteranganIndikator;
use App\Models\FormulasiTahap1;
use App\Models\FormulasiTahap2;
use App\Models\IndikatorTahap2;
use App\Models\KeteranganTahap2;

class IndikatorController extends Controller
{
    // ══════════════════════════════════════════════════════════
    // TAHAP 1 — Halaman utama
    // ══════════════════════════════════════════════════════════
    public function tahap1()
    {
        $subEvents   = SubEvent::orderBy('tahun', 'desc')->get();
        $formulasis1 = FormulasiTahap1::pluck('sub_event_id')->toArray();

        $detailValid1 = [];
        foreach ($subEvents as $se) {
            $f = FormulasiTahap1::query()->where('sub_event_id', $se->id)->first();
            $detailValid1[$se->id] = $f
                ? (($f->nilai_makalah + $f->nilai_substansi) == 100)
                : false;
        }

        return view('indikator.tahap-1', compact('subEvents', 'formulasis1', 'detailValid1'));
    }

    public function formulasiTahap1Store(Request $request, $subEventId)
    {
        $request->validate([
            'nilai_makalah'   => 'required|integer|min:1|max:100',
            'nilai_substansi' => 'required|integer|min:1|max:100',
        ]);

        if (($request->nilai_makalah + $request->nilai_substansi) !== 100) {
            return back()->withErrors(['total' => 'Total harus 100%.'])->withInput();
        }

        SubEvent::findOrFail($subEventId);

        FormulasiTahap1::updateOrCreate(
            ['sub_event_id' => $subEventId],
            [
                'nilai_makalah'   => $request->nilai_makalah,
                'nilai_substansi' => $request->nilai_substansi,
            ]
        );

        return redirect()->route('indikator.tahap1')->with('success', 'Formulasi Tahap 1 berhasil disimpan.');
    }

    public function formulasiTahap1Get($subEventId)
    {
        $f = FormulasiTahap1::query()->where('sub_event_id', $subEventId)->first();
        return response()->json($f ?? ['nilai_makalah' => 0, 'nilai_substansi' => 0]);
    }

    // ══════════════════════════════════════════════════════════
    // TAHAP 1 — Detail Inovasi
    // ══════════════════════════════════════════════════════════
    public function detailInovasi($subEventId)
    {
        $subEvent     = SubEvent::findOrFail($subEventId);
        $subEventName = $subEvent->sub_event;
        $indikators   = Indikator::query()->where('sub_event_id', $subEventId)->get();
        return view('indikator.detail_inovasi', compact('subEventId', 'subEventName', 'indikators'));
    }

    // ↓↓↓ DIUBAH: tambah validasi & simpan kolom `jenis`
    public function inovasiStore(Request $request, $subEventId)
    {
        $request->validate([
            'nama_indikator' => 'required|string|max:255',
            'jenis'          => 'required|in:makalah,substansi',
        ]);
        SubEvent::findOrFail($subEventId);
        Indikator::create([
            'sub_event_id'   => $subEventId,
            'nama_indikator' => $request->nama_indikator,
            'jenis'          => $request->jenis,
        ]);
        return redirect()->route('indikator.tahap1.inovasi', $subEventId)->with('success', 'Indikator berhasil ditambahkan.');
    }

    // ↓↓↓ DIUBAH: tambah validasi & update kolom `jenis`
    public function inovasiUpdate(Request $request, $subEventId, $id)
    {
        $request->validate([
            'nama_indikator' => 'required|string|max:255',
            'jenis'          => 'required|in:makalah,substansi',
        ]);
        Indikator::query()->where('sub_event_id', $subEventId)->findOrFail($id)->update([
            'nama_indikator' => $request->nama_indikator,
            'jenis'          => $request->jenis,
        ]);
        return redirect()->route('indikator.tahap1.inovasi', $subEventId)->with('success', 'Indikator berhasil diperbarui.');
    }

    public function inovasiDestroy($subEventId, $id)
    {
        Indikator::query()->where('sub_event_id', $subEventId)->findOrFail($id)->delete();
        return redirect()->route('indikator.tahap1.inovasi', $subEventId)->with('success', 'Indikator berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════
    // TAHAP 1 — Detail Indikator
    // ══════════════════════════════════════════════════════════
    public function detailIndikator($subEventId, $indikatorId)
    {
        SubEvent::findOrFail($subEventId);
        $indikator     = Indikator::query()->where('sub_event_id', $subEventId)->findOrFail($indikatorId);
        $indikatorName = $indikator->nama_indikator;
        $keterangans   = KeteranganIndikator::query()->where('indikator_id', $indikatorId)->get();
        return view('indikator.detail_indikator', compact('subEventId', 'indikatorId', 'indikatorName', 'keterangans'));
    }

    public function detailIndikatorStore(Request $request, $subEventId, $indikatorId)
    {
        $request->validate(['keterangan' => 'required|string|max:255', 'nilai_minimal' => 'required|integer|min:0', 'nilai_maksimal' => 'required|integer|min:0|gte:nilai_minimal']);
        Indikator::query()->where('sub_event_id', $subEventId)->findOrFail($indikatorId);
        KeteranganIndikator::create(['indikator_id' => $indikatorId, 'keterangan' => $request->keterangan, 'nilai_minimal' => $request->nilai_minimal, 'nilai_maksimal' => $request->nilai_maksimal]);
        return redirect()->route('indikator.tahap1.detail', [$subEventId, $indikatorId])->with('success', 'Keterangan berhasil ditambahkan.');
    }

    public function detailIndikatorUpdate(Request $request, $subEventId, $indikatorId, $id)
    {
        $request->validate(['keterangan' => 'required|string|max:255', 'nilai_minimal' => 'required|integer|min:0', 'nilai_maksimal' => 'required|integer|min:0|gte:nilai_minimal']);
        KeteranganIndikator::query()->where('indikator_id', $indikatorId)->findOrFail($id)->update(['keterangan' => $request->keterangan, 'nilai_minimal' => $request->nilai_minimal, 'nilai_maksimal' => $request->nilai_maksimal]);
        return redirect()->route('indikator.tahap1.detail', [$subEventId, $indikatorId])->with('success', 'Keterangan berhasil diperbarui.');
    }

    public function detailIndikatorDestroy($subEventId, $indikatorId, $id)
    {
        KeteranganIndikator::query()->where('indikator_id', $indikatorId)->findOrFail($id)->delete();
        return redirect()->route('indikator.tahap1.detail', [$subEventId, $indikatorId])->with('success', 'Keterangan berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════
    // TAHAP 2 — Halaman utama
    // ══════════════════════════════════════════════════════════
    public function tahap2()
    {
        $subEvents   = SubEvent::orderBy('tahun', 'desc')->get();
        $formulasis  = FormulasiTahap2::pluck('sub_event_id')->toArray();
        $detailValid = [];
        foreach ($subEvents as $se) {
            $f = FormulasiTahap2::query()->where('sub_event_id', $se->id)->first();
            $detailValid[$se->id] = $f ? (($f->nilai_inovasi + $f->nilai_peragaan) == 100) : false;
        }
        return view('indikator.tahap-2', compact('subEvents', 'formulasis', 'detailValid'));
    }

    public function formulasiTahap2Store(Request $request, $subEventId)
    {
        $request->validate(['nilai_inovasi' => 'required|integer|min:1|max:100', 'nilai_peragaan' => 'required|integer|min:1|max:100']);
        if (($request->nilai_inovasi + $request->nilai_peragaan) !== 100) {
            return back()->withErrors(['total' => 'Total harus 100%.'])->withInput();
        }
        SubEvent::findOrFail($subEventId);
        FormulasiTahap2::updateOrCreate(['sub_event_id' => $subEventId], ['nilai_inovasi' => $request->nilai_inovasi, 'nilai_peragaan' => $request->nilai_peragaan]);
        return redirect()->route('indikator.tahap2')->with('success', 'Formulasi berhasil disimpan.');
    }

    public function formulasiTahap2Get($subEventId)
    {
        $f = FormulasiTahap2::query()->where('sub_event_id', $subEventId)->first();
        return response()->json($f ?? ['nilai_inovasi' => 0, 'nilai_peragaan' => 0]);
    }

    // ══════════════════════════════════════════════════════════
    // TAHAP 2 — Detail Indikator
    // ══════════════════════════════════════════════════════════
    public function detailIndikator2($subEventId)
    {
        $subEvent   = SubEvent::findOrFail($subEventId);
        $indikators = IndikatorTahap2::with('keterangans')
            ->where('sub_event_id', $subEventId)
            ->has('keterangans')
            ->get();
        return view('indikator.tahap-2-detail', compact('subEvent', 'indikators'));
    }

    public function indikatorTahap2Store(Request $request, $subEventId)
    {
        $request->validate(['nama_indikator' => 'required|string|max:255', 'jenis' => 'required|in:Subtansi Inovasi,Peragaan', 'keterangan' => 'required|string|max:500', 'nilai_minimal' => 'required|integer|min:0', 'nilai_maksimal' => 'required|integer|min:0|gte:nilai_minimal']);
        SubEvent::findOrFail($subEventId);
        $indikator = IndikatorTahap2::firstOrCreate(['sub_event_id' => $subEventId, 'nama_indikator' => $request->nama_indikator, 'jenis' => $request->jenis]);
        KeteranganTahap2::create(['indikator_tahap2_id' => $indikator->id, 'keterangan' => $request->keterangan, 'nilai_minimal' => $request->nilai_minimal, 'nilai_maksimal' => $request->nilai_maksimal]);
        return redirect()->route('indikator.tahap2.indikator', $subEventId)->with('success', 'Indikator berhasil ditambahkan.');
    }

    public function indikatorTahap2Update(Request $request, $subEventId, $id)
    {
        $request->validate(['nama_indikator' => 'required|string|max:255', 'jenis' => 'required|in:Subtansi Inovasi,Peragaan', 'keterangan' => 'required|string|max:500', 'nilai_minimal' => 'required|integer|min:0', 'nilai_maksimal' => 'required|integer|min:0|gte:nilai_minimal']);
        $ket = KeteranganTahap2::findOrFail($id);
        $ket->indikator->update(['nama_indikator' => $request->nama_indikator, 'jenis' => $request->jenis]);
        $ket->update(['keterangan' => $request->keterangan, 'nilai_minimal' => $request->nilai_minimal, 'nilai_maksimal' => $request->nilai_maksimal]);
        return redirect()->route('indikator.tahap2.indikator', $subEventId)->with('success', 'Indikator berhasil diperbarui.');
    }

    public function indikatorTahap2Destroy($subEventId, $id)
    {
        $keterangan = KeteranganTahap2::findOrFail($id);
        $indikator  = $keterangan->indikator;
        $keterangan->delete();
        if ($indikator && $indikator->keterangans()->count() === 0) {
            $indikator->delete();
        }
        return redirect()->route('indikator.tahap2.indikator', $subEventId)->with('success', 'Indikator berhasil dihapus.');
    }
}