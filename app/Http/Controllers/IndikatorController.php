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
    // ══════════════════════════════════════════════════════════
    // TAHAP 1 — Halaman utama
    // ══════════════════════════════════════════════════════════
    public function tahap1()
    {
        $subEvents = SubEvent::orderBy('tahun', 'desc')->get();
        return view('indikator.tahap-1', compact('subEvents'));
    }

    public function formulasiTahap1Store(Request $request, int $subEventId)
    {
        $request->validate([
            'nilai_makalah'   => 'required|integer|min:1|max:100',
            'nilai_substansi' => 'required|integer|min:1|max:100',
        ]);
        if (($request->nilai_makalah + $request->nilai_substansi) !== 100) {
            return back()->withErrors(['total' => 'Total harus 100%.'])->withInput();
        }
        $data  = session('formulasi_tahap1', []);
        $found = false;
        foreach ($data as &$row) {
            if ($row['sub_event_id'] === $subEventId) {
                $row['nilai_makalah']   = $request->nilai_makalah;
                $row['nilai_substansi'] = $request->nilai_substansi;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $data[] = [
                'id'              => count($data) + 1,
                'sub_event_id'    => $subEventId,
                'nilai_makalah'   => $request->nilai_makalah,
                'nilai_substansi' => $request->nilai_substansi,
            ];
        }
        session(['formulasi_tahap1' => $data]);
        return redirect()->route('indikator.tahap1')->with('success', 'Formulasi Tahap 1 berhasil disimpan.');
    }

    public function formulasiTahap1Get(int $subEventId)
    {
        $f = collect(session('formulasi_tahap1', []))->firstWhere('sub_event_id', $subEventId);
        return response()->json($f ?? ['nilai_makalah' => 0, 'nilai_substansi' => 0]);
    }

    // ══════════════════════════════════════════════════════════
    // TAHAP 1 — Detail Inovasi
    // ══════════════════════════════════════════════════════════
    public function detailInovasi(int $subEventId)
    {
        $subEvent     = SubEvent::findOrFail($subEventId);
        $subEventName = $subEvent->sub_event;
        $indikators = Indikator::query()->where('sub_event_id', $subEventId)->get();
        return view('indikator.detail_inovasi', compact('subEventId', 'subEventName', 'indikators'));
    }

    public function inovasiStore(Request $request, int $subEventId)
    {
        $request->validate(['nama_indikator' => 'required|string|max:255']);
        SubEvent::findOrFail($subEventId);
        Indikator::create(['sub_event_id' => $subEventId, 'nama_indikator' => $request->nama_indikator]);
        return redirect()->route('indikator.tahap1.inovasi', $subEventId)->with('success', 'Indikator berhasil ditambahkan.');
    }

    public function inovasiUpdate(Request $request, int $subEventId, int $id)
    {
        $request->validate(['nama_indikator' => 'required|string|max:255']);
        Indikator::query()->where('sub_event_id', $subEventId)->findOrFail($id)->update(['nama_indikator' => $request->nama_indikator]);
        return redirect()->route('indikator.tahap1.inovasi', $subEventId)->with('success', 'Indikator berhasil diperbarui.');
    }

    public function inovasiDestroy(int $subEventId, int $id)
    {
        Indikator::query()->where('sub_event_id', $subEventId)->findOrFail($id)->delete($id);
        return redirect()->route('indikator.tahap1.inovasi', $subEventId)->with('success', 'Indikator berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════
    // TAHAP 1 — Detail Indikator
    // ══════════════════════════════════════════════════════════
    public function detailIndikator(int $subEventId, int $indikatorId)
    {
        SubEvent::findOrFail($subEventId);
        $indikator     = Indikator::findOrFail($indikatorId);
        $indikatorName = $indikator->nama_indikator;
        $keterangans   = KeteranganIndikator::query()->where('indikator_id', $indikatorId)->get();
        return view('indikator.detail_indikator', compact('subEventId', 'indikatorId', 'indikatorName', 'keterangans'));
    }

    public function detailIndikatorStore(Request $request, int $subEventId, int $indikatorId)
    {
        $request->validate([
            'keterangan'    => 'required|string|max:255',
            'nilai_minimal' => 'required|integer|min:0',
            'nilai_maksimal' => 'required|integer|min:0|gte:nilai_minimal',
        ]);
        Indikator::findOrFail($indikatorId);
        KeteranganIndikator::create([
            'indikator_id'  => $indikatorId,
            'keterangan'    => $request->keterangan,
            'nilai_minimal' => $request->nilai_minimal,
            'nilai_maksimal' => $request->nilai_maksimal,
        ]);
        return redirect()->route('indikator.tahap1.detail', [$subEventId, $indikatorId])->with('success', 'Keterangan berhasil ditambahkan.');
    }

    public function detailIndikatorUpdate(Request $request, int $subEventId, int $indikatorId, int $id)
    {
        $request->validate([
            'keterangan'    => 'required|string|max:255',
            'nilai_minimal' => 'required|integer|min:0',
            'nilai_maksimal' => 'required|integer|min:0|gte:nilai_minimal',
        ]);
        $keterangan = KeteranganIndikator::query()->where('indikator_id', $indikatorId)->findOrFail($id);
        $keterangan->update([
            'keterangan'    => $request->keterangan,
            'nilai_minimal' => $request->nilai_minimal,
            'nilai_maksimal' => $request->nilai_maksimal,
        ]);
        return redirect()->route('indikator.tahap1.detail', [$subEventId, $indikatorId])->with('success', 'Keterangan berhasil diperbarui.');
    }

    public function detailIndikatorDestroy(int $subEventId, int $indikatorId, int $id)
    {
        $keterangan = KeteranganIndikator::findOrFail($id);
        $keterangan->delete();
        return redirect()->route('indikator.tahap1.detail', [$subEventId, $indikatorId])->with('success', 'Keterangan berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════
    // TAHAP 2 — Halaman utama
    // ══════════════════════════════════════════════════════════
    public function tahap2()
    {
        $subEvents   = SubEvent::orderBy('tahun', 'desc')->get();
        $formulasis  = FormulasiTahap2::query()->pluck('sub_event_id')->toArray();
        $detailValid = [];
        foreach ($subEvents as $se) {
            $f = FormulasiTahap2::query()->where('sub_event_id', $se->id)->first();
            $detailValid[$se->id] = $f ? (($f->nilai_inovasi + $f->nilai_peragaan) == 100) : false;
        }
        return view('indikator.tahap-2', compact('subEvents', 'formulasis', 'detailValid'));
    }

    public function formulasiTahap2Store(Request $request, int $subEventId)
    {
        $request->validate([
            'nilai_inovasi'  => 'required|integer|min:1|max:100',
            'nilai_peragaan' => 'required|integer|min:1|max:100',
        ]);
        if (($request->nilai_inovasi + $request->nilai_peragaan) !== 100) {
            return back()->withErrors(['total' => 'Total harus 100%.'])->withInput();
        }
        SubEvent::findOrFail($subEventId);
        FormulasiTahap2::updateOrCreate(
            ['sub_event_id' => $subEventId],
            ['nilai_inovasi' => $request->nilai_inovasi, 'nilai_peragaan' => $request->nilai_peragaan]
        );
        return redirect()->route('indikator.tahap2')->with('success', 'Formulasi berhasil disimpan.');
    }

    public function formulasiTahap2Get(int $subEventId)
    {
        $f = FormulasiTahap2::query()->where('sub_event_id', '=', $subEventId)->first();
        return response()->json($f ?? ['nilai_inovasi' => 0, 'nilai_peragaan' => 0]);
    }

    // ══════════════════════════════════════════════════════════
    // TAHAP 2 — Detail Indikator
    // ══════════════════════════════════════════════════════════
    public function detailIndikator2(int $subEventId)
    {
        $subEvent   = SubEvent::findOrFail($subEventId);
        $indikators = IndikatorTahap2::with('keterangans')
            ->where('sub_event_id', $subEventId)
            ->has('keterangans')
            ->get();
        return view('indikator.tahap-2-detail', compact('subEvent', 'indikators'));
    }

    public function indikatorTahap2Store(Request $request, int $subEventId)
    {
        $request->validate([
            'nama_indikator' => 'required|string|max:255',
            'jenis'          => 'required|in:Subtansi Inovasi,Peragaan',
            'keterangan'     => 'required|string|max:500',
            'nilai_minimal'  => 'required|integer|min:0',
            'nilai_maksimal' => 'required|integer|min:0|gte:nilai_minimal',
        ]);
        SubEvent::findOrFail($subEventId);
        $indikator = IndikatorTahap2::firstOrCreate([
            'sub_event_id'   => $subEventId,
            'nama_indikator' => $request->nama_indikator,
            'jenis'          => $request->jenis,
        ]);
        KeteranganTahap2::create([
            'indikator_tahap2_id' => $indikator->id,
            'keterangan'          => $request->keterangan,
            'nilai_minimal'       => $request->nilai_minimal,
            'nilai_maksimal'      => $request->nilai_maksimal,
        ]);
        return redirect()->route('indikator.tahap2.indikator', $subEventId)->with('success', 'Indikator berhasil ditambahkan.');
    }

    public function indikatorTahap2Update(Request $request, int $subEventId, int $id)
    {
        $request->validate([
            'nama_indikator' => 'required|string|max:255',
            'jenis'          => 'required|in:Subtansi Inovasi,Peragaan',
            'keterangan'     => 'required|string|max:500',
            'nilai_minimal'  => 'required|integer|min:0',
            'nilai_maksimal' => 'required|integer|min:0|gte:nilai_minimal',
        ]);
        $ket = KeteranganTahap2::findOrFail($id);
        $ket->indikator->update([
            'nama_indikator' => $request->nama_indikator,
            'jenis'          => $request->jenis,
        ]);
        $ket->update([
            'keterangan'    => $request->keterangan,
            'nilai_minimal' => $request->nilai_minimal,
            'nilai_maksimal' => $request->nilai_maksimal,
        ]);
        return redirect()->route('indikator.tahap2.indikator', $subEventId)->with('success', 'Indikator berhasil diperbarui.');
    }

    public function indikatorTahap2Destroy(int $subEventId, int $id)
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