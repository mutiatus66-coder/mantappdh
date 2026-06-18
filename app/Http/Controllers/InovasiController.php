<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\SubEvent;
use App\Models\Usulan;
use App\Models\AnggotaTim;
use App\Models\PenilaianUsulan;
use App\Models\Pemenang;

class InovasiController extends Controller
{
    // ── Helper: hitung nilai akhir dari rows nilai ────────────────────────
    // Logika sama dengan PenilaianController::getUsulanSplit():
    // avg nilai per penilai → avg semua penilai
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
        return view('inovasi.rekapnilai', compact('subEvents'));
    }

    // ── Halaman kelola usulan (form modal + daftar) per sub event ─────────

    public function usulan($subEventId)
    {
        $subEvent = SubEvent::with('event', 'bidangs')->findOrFail($subEventId);

        $usulans = Usulan::with('anggotaTim', 'bidang')
            ->where('user_id', Auth::id())
            ->where('sub_event_id', $subEventId)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('inovasi.usulan', compact('subEvent', 'usulans'));
    }

    // ── BUG FIX 3: Rekap semua pendaftar per sub event (admin) ───────────
    // Sebelumnya: nilai_t1/t2/total hardcode '-'
    // Sesudahnya: load dari penilaian_usulan (t1) dan pemenang (t2),
    //             logika avg konsisten dengan PenilaianController
    public function rekapPendaftar($subEventId)
    {
        $subEvent     = SubEvent::with('event')->findOrFail($subEventId);
        $subEventNama = $subEvent->sub_event;

        $usulans = Usulan::with('bidang')
            ->where('sub_event_id', $subEventId)
            ->get();

        $usulanIds = $usulans->pluck('id')->toArray();

        // Load semua baris nilai t1 & t2 sekaligus, group by usulan_id
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

    // ── Riwayat usulan milik peserta ──────────────────────────────────────

    public function usulanRiwayat($subEventId)
    {
        $subEvent     = SubEvent::with('event')->findOrFail($subEventId);
        $subEventNama = $subEvent->sub_event;
        $eventNama    = $subEvent->event->nama_event ?? '-';

        $usulan = Usulan::with('bidang', 'anggotaTim')
            ->where('user_id', Auth::id())
            ->where('sub_event_id', $subEventId)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('inovasi.usulan_riwayat', compact('usulan', 'subEventNama', 'eventNama'));
    }

    // ── BUG FIX 4: Rekap nilai usulan milik peserta ───────────────────────
    // Sebelumnya: tidak load nilai sama sekali dari DB
    // Sesudahnya: load dari penilaian_usulan (t1) dan pemenang (t2)
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

    // ── CRUD Usulan ───────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Halaman 1
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
            // Halaman 2
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
            // Halaman 3
            'file_surat_pernyataan' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'file_proposal'         => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'file_gambar'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'link_video'            => 'nullable|url|max:255',
            // Anggota
            'anggota'               => 'nullable|array',
            'anggota.*'             => 'nullable|string|max:255',
        ]);

        $data             = $validated;
        $data['user_id']  = Auth::id();
        $data['status']   = 'Melengkapi Data';
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

        return response()->json(['success' => true, 'message' => 'Usulan berhasil dikirim!']);
    }
}