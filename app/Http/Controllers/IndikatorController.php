<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\SubEvent;
use App\Models\Indikator;
use App\Models\KeteranganIndikator;
use App\Models\FormulasiTahap1;
use App\Models\FormulasiTahap2;
use App\Models\IndikatorTahap2;
use App\Models\KeteranganTahap2;

class IndikatorController extends Controller
{
    /* ══════════════════════════════════════════════════════════
       HELPER PRIVATE: VALIDASI TOTAL FORMULASI
       Digunakan oleh store Tahap 1 & 2 untuk memastikan
       total bobot = 100 sebelum disimpan ke database.
    ══════════════════════════════════════════════════════════ */
    private function validateTotal(int $total, string $field = 'total'): void
    {
        if ($total !== 100) {
            // Redirect back dengan error bag 'total'
            abort(
                redirect()->back()
                    ->withErrors([$field => 'Total bobot harus 100%.'])
                    ->withInput()
            );
        }
    }

    /* ══════════════════════════════════════════════════════════
       HELPER PRIVATE: HITUNG STATUS DETAIL VALID
       Menentukan apakah tombol "Detail Indikator" boleh aktif
       untuk setiap sub event (formulasi harus sudah = 100%).
    ══════════════════════════════════════════════════════════ */
    private function buildDetailValid(
        \Illuminate\Database\Eloquent\Collection $subEvents,
        string $modelClass,
        string $field1,
        string $field2
    ): array {
        $detailValid = [];
        foreach ($subEvents as $se) {
            $f = $modelClass::query()->where('sub_event_id', $se->id)->first();
            $detailValid[$se->id] = $f
                ? (($f->$field1 + $f->$field2) === 100)
                : false;
        }
        return $detailValid;
    }


    // ══════════════════════════════════════════════════════════
    // TAHAP 1 — Halaman Utama
    // Menampilkan daftar sub event + status formulasi & detail
    // ══════════════════════════════════════════════════════════

    public function tahap1()
    {
        $subEvents   = SubEvent::orderBy('tahun', 'desc')->get();
        $formulasis1 = FormulasiTahap1::pluck('sub_event_id')->toArray();

        // Cek apakah formulasi tiap sub event sudah valid (total = 100)
        $detailValid1 = $this->buildDetailValid(
            $subEvents,
            FormulasiTahap1::class,
            'nilai_makalah',
            'nilai_substansi'
        );

        return view('indikator.tahap-1', compact('subEvents', 'formulasis1', 'detailValid1'));
    }

    /* ══════════════════════════════════════════════════════════
       TAHAP 1 — Simpan / Update Formulasi
       Menggunakan updateOrCreate agar satu sub event
       hanya bisa punya satu formulasi (upsert).
    ══════════════════════════════════════════════════════════ */
    public function formulasiTahap1Store(Request $request, int $subEventId)
    {
        $request->validate([
            'nilai_makalah'   => 'required|integer|min:1|max:100',
            'nilai_substansi' => 'required|integer|min:1|max:100',
        ]);

        // Validasi total = 100
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

        return redirect()->route('indikator.tahap1')
            ->with('success', 'Formulasi Tahap 1 berhasil disimpan.');
    }

    /* ══════════════════════════════════════════════════════════
       TAHAP 1 — Get Formulasi (AJAX)
       Dipanggil JS saat modal dibuka untuk sub event
       yang sudah punya formulasi.
    ══════════════════════════════════════════════════════════ */
    public function formulasiTahap1Get(int $subEventId): JsonResponse
    {
        $f = FormulasiTahap1::query()->where('sub_event_id', $subEventId)->first();

        return response()->json($f ?? ['nilai_makalah' => 0, 'nilai_substansi' => 0]);
    }


    // ══════════════════════════════════════════════════════════
    // TAHAP 1 — Detail Inovasi (Daftar Indikator per Sub Event)
    // ══════════════════════════════════════════════════════════

    public function detailInovasi(int $subEventId)
    {
        $subEvent     = SubEvent::findOrFail($subEventId);
        $subEventName = $subEvent->sub_event;
        $indikators   = Indikator::query()
            ->where('sub_event_id', $subEventId)
            ->orderBy('id')
            ->get();

        return view('indikator.detail_inovasi', compact('subEventId', 'subEventName', 'indikators'));
    }

    /* ══════════════════════════════════════════════════════════
       TAHAP 1 — Simpan Indikator Baru
       Menyimpan kolom `jenis` (makalah / substansi)
       sesuai pemilihan di form.
    ══════════════════════════════════════════════════════════ */
    public function inovasiStore(Request $request, int $subEventId)
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

        return redirect()
            ->route('indikator.tahap1.inovasi', $subEventId)
            ->with('success', 'Indikator berhasil ditambahkan.');
    }

    /* ══════════════════════════════════════════════════════════
       TAHAP 1 — Update Indikator
       Scope query ke sub_event_id agar tidak bisa edit
       indikator milik sub event lain.
    ══════════════════════════════════════════════════════════ */
    public function inovasiUpdate(Request $request, int $subEventId, int $id)
    {
        $request->validate([
            'nama_indikator' => 'required|string|max:255',
            'jenis'          => 'required|in:makalah,substansi',
        ]);

        Indikator::query()
            ->where('sub_event_id', $subEventId)
            ->findOrFail($id)
            ->update([
                'nama_indikator' => $request->nama_indikator,
                'jenis'          => $request->jenis,
            ]);

        return redirect()
            ->route('indikator.tahap1.inovasi', $subEventId)
            ->with('success', 'Indikator berhasil diperbarui.');
    }

    /* ══════════════════════════════════════════════════════════
       TAHAP 1 — Hapus Indikator
    ══════════════════════════════════════════════════════════ */
    public function inovasiDestroy(int $subEventId, int $id)
    {
        Indikator::query()
            ->where('sub_event_id', $subEventId)
            ->findOrFail($id)
            ->delete();

        return redirect()
            ->route('indikator.tahap1.inovasi', $subEventId)
            ->with('success', 'Indikator berhasil dihapus.');
    }


    // ══════════════════════════════════════════════════════════
    // TAHAP 1 — Detail Indikator (Keterangan Nilai per Indikator)
    // ══════════════════════════════════════════════════════════

    public function detailIndikator(int $subEventId, int $indikatorId)
    {
        SubEvent::findOrFail($subEventId);

        $indikator     = Indikator::query()
            ->where('sub_event_id', $subEventId)
            ->findOrFail($indikatorId);
        $indikatorName = $indikator->nama_indikator;
        $keterangans   = KeteranganIndikator::query()
            ->where('indikator_id', $indikatorId)
            ->orderBy('id')
            ->get();

        return view('indikator.detail_indikator', compact(
            'subEventId', 'indikatorId', 'indikatorName', 'keterangans'
        ));
    }

    /* ══════════════════════════════════════════════════════════
       TAHAP 1 — Simpan Keterangan Indikator
       Validasi nilai_maksimal >= nilai_minimal via rule 'gte'
    ══════════════════════════════════════════════════════════ */
    public function detailIndikatorStore(Request $request, int $subEventId, int $indikatorId)
    {
        $request->validate([
            'keterangan'    => 'required|string|max:255',
            'nilai_minimal' => 'required|integer|min:0',
            'nilai_maksimal'=> 'required|integer|min:0|gte:nilai_minimal',
        ]);

        Indikator::query()->where('sub_event_id', $subEventId)->findOrFail($indikatorId);

        KeteranganIndikator::create([
            'indikator_id'  => $indikatorId,
            'keterangan'    => $request->keterangan,
            'nilai_minimal' => $request->nilai_minimal,
            'nilai_maksimal'=> $request->nilai_maksimal,
        ]);

        return redirect()
            ->route('indikator.tahap1.detail', [$subEventId, $indikatorId])
            ->with('success', 'Keterangan berhasil ditambahkan.');
    }

    /* ══════════════════════════════════════════════════════════
       TAHAP 1 — Update Keterangan Indikator
    ══════════════════════════════════════════════════════════ */
    public function detailIndikatorUpdate(Request $request, int $subEventId, int $indikatorId, int $id)
    {
        $request->validate([
            'keterangan'    => 'required|string|max:255',
            'nilai_minimal' => 'required|integer|min:0',
            'nilai_maksimal'=> 'required|integer|min:0|gte:nilai_minimal',
        ]);

        KeteranganIndikator::query()
            ->where('indikator_id', $indikatorId)
            ->findOrFail($id)
            ->update([
                'keterangan'    => $request->keterangan,
                'nilai_minimal' => $request->nilai_minimal,
                'nilai_maksimal'=> $request->nilai_maksimal,
            ]);

        return redirect()
            ->route('indikator.tahap1.detail', [$subEventId, $indikatorId])
            ->with('success', 'Keterangan berhasil diperbarui.');
    }

    /* ══════════════════════════════════════════════════════════
       TAHAP 1 — Hapus Keterangan Indikator
    ══════════════════════════════════════════════════════════ */
    public function detailIndikatorDestroy(int $subEventId, int $indikatorId, int $id)
    {
        KeteranganIndikator::query()
            ->where('indikator_id', $indikatorId)
            ->findOrFail($id)
            ->delete();

        return redirect()
            ->route('indikator.tahap1.detail', [$subEventId, $indikatorId])
            ->with('success', 'Keterangan berhasil dihapus.');
    }


    // ══════════════════════════════════════════════════════════
    // TAHAP 2 — Halaman Utama
    // ══════════════════════════════════════════════════════════

    public function tahap2()
    {
        $subEvents  = SubEvent::orderBy('tahun', 'desc')->get();
        $formulasis = FormulasiTahap2::pluck('sub_event_id')->toArray();

        // Cek validitas formulasi per sub event
        $detailValid = $this->buildDetailValid(
            $subEvents,
            FormulasiTahap2::class,
            'nilai_inovasi',
            'nilai_peragaan'
        );

        return view('indikator.tahap-2', compact('subEvents', 'formulasis', 'detailValid'));
    }

    /* ══════════════════════════════════════════════════════════
       TAHAP 2 — Simpan / Update Formulasi
    ══════════════════════════════════════════════════════════ */
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
            [
                'nilai_inovasi'  => $request->nilai_inovasi,
                'nilai_peragaan' => $request->nilai_peragaan,
            ]
        );

        return redirect()
            ->route('indikator.tahap2')
            ->with('success', 'Formulasi berhasil disimpan.');
    }

    /* ══════════════════════════════════════════════════════════
       TAHAP 2 — Get Formulasi (AJAX)
    ══════════════════════════════════════════════════════════ */
    public function formulasiTahap2Get(int $subEventId): JsonResponse
    {
        $f = FormulasiTahap2::query()->where('sub_event_id', $subEventId)->first();

        return response()->json($f ?? ['nilai_inovasi' => 0, 'nilai_peragaan' => 0]);
    }


    // ══════════════════════════════════════════════════════════
    // TAHAP 2 — Detail Indikator per Sub Event
    // Eager load keterangans agar tidak N+1 query
    // ══════════════════════════════════════════════════════════

    public function detailIndikator2(int $subEventId)
    {
        $subEvent   = SubEvent::findOrFail($subEventId);
        $indikators = IndikatorTahap2::with('keterangans')
            ->where('sub_event_id', $subEventId)
            ->orderBy('jenis')
            ->orderBy('id')
            ->get();

        return view('indikator.tahap-2-detail', compact('subEvent', 'indikators'));
    }

    /* ══════════════════════════════════════════════════════════
       TAHAP 2 — Simpan Indikator + Keterangan
       Menggunakan firstOrCreate pada IndikatorTahap2 agar
       tidak duplikat indikator dengan nama & jenis sama,
       lalu buat KeteranganTahap2 baru di bawahnya.
    ══════════════════════════════════════════════════════════ */
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

        // Reuse indikator yang sudah ada jika nama & jenis sama
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

        return redirect()
            ->route('indikator.tahap2.indikator', $subEventId)
            ->with('success', 'Indikator berhasil ditambahkan.');
    }

    /* ══════════════════════════════════════════════════════════
       TAHAP 2 — Update Indikator + Keterangan
       Update dilakukan pada KeteranganTahap2 ($id = ket.id),
       lalu update parent IndikatorTahap2 via relasi.
    ══════════════════════════════════════════════════════════ */
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

        // Update parent indikator (nama & jenis)
        $ket->indikator->update([
            'nama_indikator' => $request->nama_indikator,
            'jenis'          => $request->jenis,
        ]);

        // Update keterangan (nilai dan teks)
        $ket->update([
            'keterangan'    => $request->keterangan,
            'nilai_minimal' => $request->nilai_minimal,
            'nilai_maksimal'=> $request->nilai_maksimal,
        ]);

        return redirect()
            ->route('indikator.tahap2.indikator', $subEventId)
            ->with('success', 'Indikator berhasil diperbarui.');
    }

    /* ══════════════════════════════════════════════════════════
       TAHAP 2 — Hapus Keterangan
       Jika setelah hapus indikator tidak punya keterangan
       lagi, hapus juga indikatornya (cascade manual).
    ══════════════════════════════════════════════════════════ */
    public function indikatorTahap2Destroy(int $subEventId, int $id)
    {
        $keterangan = KeteranganTahap2::findOrFail($id);
        $indikator  = $keterangan->indikator;

        $keterangan->delete();

        // Hapus indikator jika sudah tidak punya keterangan
        if ($indikator && $indikator->keterangans()->count() === 0) {
            $indikator->delete();
        }

        return redirect()
            ->route('indikator.tahap2.indikator', $subEventId)
            ->with('success', 'Indikator berhasil dihapus.');
    }
}