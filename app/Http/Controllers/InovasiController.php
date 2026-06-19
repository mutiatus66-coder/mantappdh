<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\SubEvent;
use App\Models\Usulan;
use App\Models\AnggotaTim;
use App\Models\PenilaianUsulan;
use App\Models\Pemenang;

class InovasiController extends Controller
{
    // ── Helper: hitung nilai akhir dari rows nilai ────────────────────────
    private function hitungNilaiAkhir($rows): float|string
    {
        if ($rows->isEmpty()) return '-';

        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row->penilai_id][] = $row->nilai;
        }

        $avgPerPenilai = [];
        foreach ($grouped as $nilaiArr) {
            $avgPerPenilai[] = array_sum($nilaiArr) / count($nilaiArr);
        }

        return round(array_sum($avgPerPenilai) / count($avgPerPenilai), 2);
    }

    // ── Halaman pintu masuk peserta ───────────────────────────────────────
    public function riwayat()
    {
        $subEvents = SubEvent::with('event')->orderBy('tahun', 'desc')->get();
        return view('inovasi.riwayat', compact('subEvents'));
    }

    public function rekapNilai()
{
    $subEvents = SubEvent::with('event')->orderBy('tahun', 'desc')->get();

    foreach ($subEvents as $se) {
        // Total usulan pada sub event ini (samakan dengan daftar di Rekap Pendaftar)
        $usulanIds = Usulan::where('sub_event_id', $se->id)->pluck('id');

        $se->inovasi_count = $usulanIds->count();

        // Jumlah usulan yang sudah punya minimal 1 nilai (Tahap 1)
        $se->dinilai_count = $usulanIds->isEmpty()
            ? 0
            : PenilaianUsulan::whereIn('usulan_id', $usulanIds)
                ->distinct('usulan_id')
                ->count('usulan_id');
    }

    return view('inovasi.rekapnilai', compact('subEvents'));
}

    // ── Halaman kelola usulan (form modal + daftar) per sub event ─────────
    // UC-07: KHUSUS PESERTA. Admin Bapperida diarahkan ke halaman Riwayat (UC-09).
    public function usulan($subEventId)
    {
        if (Auth::user()->isAdminBapperida()) {
            return redirect()->route('inovasi.usulan-riwayat', $subEventId);
        }

        $subEvent = SubEvent::with('event', 'bidangs')->findOrFail($subEventId);

        $usulans = Usulan::with('anggotaTim', 'bidang')
            ->where('user_id', Auth::id())
            ->where('sub_event_id', $subEventId)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('inovasi.usulan', compact('subEvent', 'usulans'));
    }

    // ── Rekap semua pendaftar per sub event (admin) ───────────────────────
    public function rekapPendaftar($subEventId)
    {
        $subEvent     = SubEvent::with('event')->findOrFail($subEventId);
        $subEventNama = $subEvent->sub_event;

        $usulans = Usulan::with('bidang')
            ->where('sub_event_id', $subEventId)
            ->get();

        $usulanIds = $usulans->pluck('id')->toArray();

        $nilaiT1Rows = PenilaianUsulan::whereIn('usulan_id', $usulanIds)->get()->groupBy('usulan_id');
        $nilaiT2Rows = Pemenang::whereIn('usulan_id', $usulanIds)->get()->groupBy('usulan_id');

        $usulan = $usulans->map(function ($u) use ($nilaiT1Rows, $nilaiT2Rows) {
            $t1 = $this->hitungNilaiAkhir($nilaiT1Rows->get($u->id) ?? collect());
            $t2 = $this->hitungNilaiAkhir($nilaiT2Rows->get($u->id) ?? collect());
            $total = ($t1 !== '-' && $t2 !== '-')
                ? round(((float)$t1 + (float)$t2) / 2, 2)
                : ($t1 !== '-' ? $t1 : ($t2 !== '-' ? $t2 : '-'));

            return [
                'judul'        => $u->judul ?? '',
                'instansi'     => $u->inovator ?? '',
                'link_youtube' => $u->link_video ?? '',
                'no_hp'        => $u->ketua_wa ?? '',
                'kategori'     => $u->kategori ?? '',
                'nilai_t1'     => $t1,
                'nilai_t2'     => $t2,
                'nilai_total'  => $total,
            ];
        });

        return view('inovasi.rekap_pendaftar', compact('subEventNama', 'usulan'));
    }

    // ── Riwayat usulan (UC-08 & UC-09) ─────────────────────────────────────
    // PERUBAHAN: Admin Bapperida melihat SEMUA usulan yang sudah dikirim;
    //            Peserta hanya melihat usulan miliknya sendiri.
    public function usulanRiwayat($subEventId)
    {
        $subEvent     = SubEvent::with('event')->findOrFail($subEventId);
        $subEventNama = $subEvent->sub_event;
        $eventNama    = $subEvent->event->nama_event ?? '-';

        $isAdmin = Auth::user()->isAdminBapperida();

        $query = Usulan::with('bidang', 'anggotaTim', 'user')
            ->where('sub_event_id', $subEventId);

        if ($isAdmin) {
            // UC-09: semua usulan yang SUDAH DIKIRIM pada sub event ini
            $query->submitted();
        } else {
            // UC-08: peserta hanya usulan miliknya
            $query->where('user_id', Auth::id());
        }

        $usulan = $query->orderBy('updated_at', 'desc')->get();

        // Tandai usulan yang sudah ada nilainya (agar status tidak bisa direset admin)
        $sudahDinilaiIds = PenilaianUsulan::whereIn('usulan_id', $usulan->pluck('id'))
            ->distinct()
            ->pluck('usulan_id')
            ->toArray();

        $usulan->each(function ($u) use ($sudahDinilaiIds) {
            $u->sudah_dinilai = in_array($u->id, $sudahDinilaiIds);
        });

        return view('inovasi.usulan_riwayat', compact('usulan', 'subEventNama', 'eventNama', 'isAdmin'));
    }

    // ── Rekap nilai usulan milik peserta ──────────────────────────────────
    public function usulanNilai($subEventId)
    {
        $subEvent     = SubEvent::with('event')->findOrFail($subEventId);
        $subEventNama = $subEvent->sub_event;
        $eventNama    = $subEvent->event->nama_event ?? '-';

        $usulan = Usulan::query()
            ->where('user_id', Auth::id())
            ->where('sub_event_id', $subEventId)
            ->orderBy('updated_at', 'desc')
            ->get();

        $usulanIds = $usulan->pluck('id')->toArray();

        $nilaiT1Rows = PenilaianUsulan::whereIn('usulan_id', $usulanIds)->get()->groupBy('usulan_id');
        $nilaiT2Rows = Pemenang::whereIn('usulan_id', $usulanIds)->get()->groupBy('usulan_id');

        $usulan->each(function ($u) use ($nilaiT1Rows, $nilaiT2Rows) {
            $t1 = $this->hitungNilaiAkhir($nilaiT1Rows->get($u->id) ?? collect());
            $t2 = $this->hitungNilaiAkhir($nilaiT2Rows->get($u->id) ?? collect());
            $u->nilai_t1    = $t1;
            $u->nilai_t2    = $t2;
            $u->nilai_total = ($t1 !== '-' && $t2 !== '-')
                ? round(((float)$t1 + (float)$t2) / 2, 2)
                : ($t1 !== '-' ? $t1 : ($t2 !== '-' ? $t2 : '-'));
        });

        return view('inovasi.usulan_nilai', compact('usulan', 'subEventNama', 'eventNama'));
    }

    // ── CRUD Usulan (KHUSUS PESERTA / pemilik usulan) ─────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sub_event_id'  => 'required|exists:sub_events,id',
            'bidang_id'     => 'required|exists:bidangs,id',
            'judul'         => 'required|string|max:255',
            'inovator'      => 'required|string|max:255',
            'nama_inovasi'  => 'required|string|max:255',
            'interaksi'     => 'required|string|max:100',
            'nama_tim'      => 'nullable|string|max:255',
            'ketua_nama'    => 'required|string|max:255',
            'ketua_email'   => 'required|email|max:255',
            'ketua_wa'      => 'required|string|max:20',
            'alamat_ketua'  => 'required|string|max:255',
            'ktp'           => 'required|string|max:50',
            'kategori'      => 'required|in:umum,pelajar',
            'asal_sekolah'  => 'required_if:kategori,pelajar|nullable|string|max:255',
            'nama_guru'     => 'nullable|string|max:150',
            'latar_belakang'        => 'required|string',
            'kondisi_sebelumnya'    => 'required|string',
            'sasaran_tujuan'        => 'required|string',
            'materi_inovasi'        => 'nullable|string',
            'deskripsi'             => 'required|string',
            'bahan_baku'            => 'nullable|string',
            'cara_kerja'            => 'required|string',
            'keunggulan'            => 'required|string',
            'hasil_diharapkan'      => 'required|string',
            'manfaat'               => 'required|string',
            'rencana_berkelanjutan' => 'required|string',
            'file_surat_pernyataan' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'file_proposal'         => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'file_gambar'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'link_video'            => 'nullable|url|max:255',
            'anggota'               => 'nullable|array',
            'anggota.*'             => 'nullable|string|max:255',
        ]);

        $data                 = $validated;
        $data['user_id']      = Auth::id();
        $data['status']       = 'Melengkapi Data';
        $data['is_submitted'] = false;
        unset($data['anggota']);

        foreach (['file_surat_pernyataan', 'file_proposal', 'file_gambar'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('usulan/' . $field, 'public');
            } else {
                unset($data[$field]);
            }
        }

        $usulan = Usulan::create($data);

        \App\Models\Inovator::firstOrCreate(
            [
                'sub_event_id' => $data['sub_event_id'],
                'inovator'     => $data['inovator'],
                'nama_inovasi' => $data['nama_inovasi'],
            ],
            [
                'kategori' => $data['kategori'],
            ]
        );

        if (!empty($validated['anggota'])) {
            foreach ($validated['anggota'] as $nama) {
                if (!empty(trim($nama))) {
                    $usulan->anggotaTim()->create(['nama_anggota' => trim($nama)]);
                }
            }
        }

        Log::info('TAMBAH USULAN', ['user' => Auth::user()->email, 'usulan_id' => $usulan->id, 'nama_inovasi' => $usulan->nama_inovasi]);
        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil disimpan.',
            'usulan'  => $usulan->load('anggotaTim', 'bidang'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $usulan = Usulan::query()
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($usulan->is_submitted) {
            return response()->json([
                'success' => false,
                'message' => 'Usulan yang sudah dikirim tidak dapat diedit.',
            ], 422);
        }

        $validated = $request->validate([
            'bidang_id'     => 'required|exists:bidangs,id',
            'judul'         => 'required|string|max:255',
            'inovator'      => 'required|string|max:255',
            'nama_inovasi'  => 'required|string|max:255',
            'interaksi'     => 'required|string|max:100',
            'nama_tim'      => 'nullable|string|max:255',
            'ketua_nama'    => 'required|string|max:255',
            'ketua_email'   => 'required|email|max:255',
            'ketua_wa'      => 'required|string|max:20',
            'alamat_ketua'  => 'required|string|max:255',
            'ktp'           => 'required|string|max:50',
            'kategori'      => 'required|in:umum,pelajar',
            'asal_sekolah'  => 'required_if:kategori,pelajar|nullable|string|max:255',
            'nama_guru'     => 'nullable|string|max:150',
            'latar_belakang'        => 'required|string',
            'kondisi_sebelumnya'    => 'required|string',
            'sasaran_tujuan'        => 'required|string',
            'materi_inovasi'        => 'nullable|string',
            'deskripsi'             => 'required|string',
            'bahan_baku'            => 'nullable|string',
            'cara_kerja'            => 'required|string',
            'keunggulan'            => 'required|string',
            'hasil_diharapkan'      => 'required|string',
            'manfaat'               => 'required|string',
            'rencana_berkelanjutan' => 'required|string',
            'file_surat_pernyataan' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'file_proposal'         => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'file_gambar'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'link_video'            => 'nullable|url|max:255',
            'anggota'               => 'nullable|array',
            'anggota.*'             => 'nullable|string|max:255',
        ]);

        $data = $validated;
        unset($data['anggota']);

        foreach (['file_surat_pernyataan', 'file_proposal', 'file_gambar'] as $field) {
            if ($request->hasFile($field)) {
                if ($usulan->$field) {
                    Storage::disk('public')->delete($usulan->$field);
                }
                $data[$field] = $request->file($field)->store('usulan/' . $field, 'public');
            } else {
                unset($data[$field]);
            }
        }

        $usulan->update($data);

        \App\Models\Inovator::updateOrCreate(
            [
                'sub_event_id' => $usulan->sub_event_id,
                'inovator'     => $validated['inovator'],
                'nama_inovasi' => $validated['nama_inovasi'],
            ],
            [
                'kategori' => $validated['kategori'],
            ]
        );

        $usulan->anggotaTim()->delete();
        if (!empty($validated['anggota'])) {
            foreach ($validated['anggota'] as $nama) {
                if (!empty(trim($nama))) {
                    $usulan->anggotaTim()->create(['nama_anggota' => trim($nama)]);
                }
            }
        }
        Log::info('EDIT STATUS USULAN', ['admin' => Auth::user()->email, 'usulan_id' => $usulan->id, 'status_baru' => $usulan->status]);
        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil diperbarui.',
            'usulan'  => $usulan->fresh()->load('anggotaTim', 'bidang'),
        ]);
    }

    public function destroy($id)
    {
        $usulan = Usulan::query()
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($usulan->is_submitted) {
            return response()->json([
                'success' => false,
                'message' => 'Usulan yang sudah dikirim tidak dapat dihapus.',
            ], 422);
        }

        foreach (['file_surat_pernyataan', 'file_proposal', 'file_gambar'] as $field) {
            if ($usulan->$field) {
                Storage::disk('public')->delete($usulan->$field);
            }
        }

        $usulan->delete();

        return response()->json(['success' => true, 'message' => 'Usulan berhasil dihapus.']);
    }

    public function kirim($id)
    {
        $usulan = Usulan::query()
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($usulan->is_submitted) {
            return response()->json([
                'success' => false,
                'message' => 'Usulan sudah pernah dikirim.',
            ], 422);
        }

        $required = ['latar_belakang', 'deskripsi', 'cara_kerja', 'keunggulan', 'hasil_diharapkan', 'manfaat'];
        foreach ($required as $field) {
            if (empty($usulan->$field)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lengkapi semua field wajib (narasi inovasi) sebelum mengirim.',
                ], 422);
            }
        }

        $usulan->update([
            'is_submitted' => true,
            'status'       => 'Sedang Dinilai',
        ]);
        Log::info('KIRIM USULAN', ['user' => Auth::user()->email, 'usulan_id' => $usulan->id, 'nama_inovasi' => $usulan->nama_inovasi]);
        return response()->json(['success' => true, 'message' => 'Usulan berhasil dikirim!']);
    }

    // ══════════════════════════════════════════════════════════════════════
    //  BARU — UC-09: Edit Status oleh Admin Bapperida
    //  Mengubah status usulan. Reset ke "Melengkapi Data" hanya boleh selama
    //  belum ada penilai yang menginput nilai.
    // ══════════════════════════════════════════════════════════════════════
    public function editStatus(Request $request, $id)
    {
        if (! Auth::user()->isAdminBapperida()) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'status' => 'required|in:Melengkapi Data,Sedang Dinilai,Selesai',
        ]);

        $usulan = Usulan::findOrFail($id);

        if ($request->status === 'Melengkapi Data') {
            $sudahDinilai = PenilaianUsulan::query()->where('usulan_id', $usulan->id)->exists();
            if ($sudahDinilai) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status tidak dapat dikembalikan ke "Melengkapi Data" karena sudah ada penilai yang memberikan nilai.',
                ], 422);
            }
            // Buka kunci agar peserta dapat melengkapi & mengirim ulang.
            $usulan->is_submitted = false;
        }

        $usulan->status = $request->status;
        $usulan->save();

        Log::info('EDIT STATUS USULAN', ['admin' => Auth::user()->email, 'usulan_id' => $usulan->id, 'status_baru' => $usulan->status]);
        return response()->json([
            'success' => true,
            'message' => 'Status usulan berhasil diperbarui.',
            'status'  => $usulan->status,
        ]);
    }
}