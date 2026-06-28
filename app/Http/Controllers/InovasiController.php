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
    // ══════════════════════════════════════════════════════════════════════
    // KEAMANAN UPLOAD FILE
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Magic bytes (isi awal file) untuk setiap tipe yang diizinkan.
     * Cara kerja: baca beberapa byte pertama file, cocokkan dengan signature asli.
     * Hacker yang ganti nama hack.php → hack.pdf tetap tertolak di sini.
     */
    private array $magicBytes = [
        'pdf'  => ["\x25\x50\x44\x46"],                          // %PDF
        'doc'  => ["\xD0\xCF\x11\xE0"],                          // OLE2 (doc lama)
        'docx' => ["\x50\x4B\x03\x04"],                          // ZIP (docx = zip)
        'jpg'  => ["\xFF\xD8\xFF"],                               // JPEG
        'jpeg' => ["\xFF\xD8\xFF"],
        'png'  => ["\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"],          // PNG
    ];

    /**
     * Validasi magic bytes file — cek isi asli, bukan hanya ekstensi/MIME.
     */
    private function isFileSafe(\Illuminate\Http\UploadedFile $file, array $allowedExts): bool
    {
        $ext     = strtolower($file->getClientOriginalExtension());
        $handle  = fopen($file->getRealPath(), 'rb');
        $header  = fread($handle, 8);
        fclose($handle);

        if (!isset($this->magicBytes[$ext])) return false;

        foreach ($this->magicBytes[$ext] as $signature) {
            if (str_starts_with($header, $signature)) return true;
        }

        return false;
    }

    /**
     * Simpan file dengan nama acak (tidak bisa ditebak) dan tanpa ekstensi berbahaya.
     */
    private function storeFileSafe(\Illuminate\Http\UploadedFile $file, string $folder): string
    {
        $ext      = strtolower($file->getClientOriginalExtension());
        $safeName = bin2hex(random_bytes(16)) . '.' . $ext; // nama acak
        $file->storeAs('usulan/' . $folder, $safeName, 'public');
        return 'usulan/' . $folder . '/' . $safeName;
    }

    /**
     * Validasi + simpan file upload — dipakai di store() dan update().
     * Mengembalikan path atau null jika tidak ada file.
     * Melempar ValidationException jika file tidak aman.
     */
    private function handleFileUpload(
        Request $request,
        string  $field,
        array   $allowedExts,
        int     $maxMB,
        string  $folder,
        ?string $oldPath = null
    ): ?string {
        if (!$request->hasFile($field)) return null;

        $file = $request->file($field);

        // 1. Cek ukuran
        if ($file->getSize() > $maxMB * 1024 * 1024) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                $field => "Ukuran file melebihi batas {$maxMB} MB.",
            ]);
        }

        // 2. Cek ekstensi
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, $allowedExts)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                $field => 'Format file tidak diizinkan. Gunakan: ' . implode(', ', $allowedExts),
            ]);
        }

        // 3. Cek magic bytes (isi asli file)
        if (!$this->isFileSafe($file, $allowedExts)) {
            Log::warning('UPLOAD MENCURIGAKAN', [
                'user'  => Auth::user()->email,
                'field' => $field,
                'file'  => $file->getClientOriginalName(),
                'mime'  => $file->getMimeType(),
            ]);
            throw \Illuminate\Validation\ValidationException::withMessages([
                $field => 'File tidak valid atau terindikasi berbahaya.',
            ]);
        }

        // 4. Hapus file lama jika ada
        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        // 5. Simpan dengan nama acak
        return $this->storeFileSafe($file, $folder);
    }

    // ══════════════════════════════════════════════════════════════════════
    // HELPER NILAI
    // ══════════════════════════════════════════════════════════════════════

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

    // ══════════════════════════════════════════════════════════════════════
    // PAGES
    // ══════════════════════════════════════════════════════════════════════

    public function riwayat()
    {
        $subEvents = SubEvent::with('event')->orderBy('tahun', 'desc')->get();
        return view('inovasi.riwayat', compact('subEvents'));
    }

    public function rekapNilai()
    {
        $subEvents   = SubEvent::with('event')->orderBy('tahun', 'desc')->get();
        $subEventIds = $subEvents->pluck('id')->toArray();

        $usulanPerSub = Usulan::whereIn('sub_event_id', $subEventIds)
            ->get(['id', 'sub_event_id'])
            ->groupBy('sub_event_id');

        $allUsulanIds = $usulanPerSub->flatten()->pluck('id')->toArray();

        $dinilaiIds = empty($allUsulanIds)
            ? collect()
            : PenilaianUsulan::whereIn('usulan_id', $allUsulanIds)->distinct()->pluck('usulan_id');

        foreach ($subEvents as $se) {
            $ids = ($usulanPerSub->get($se->id) ?? collect())->pluck('id');
            $se->inovasi_count = $ids->count();
            $se->dinilai_count = $ids->intersect($dinilaiIds)->count();
        }

        return view('inovasi.rekapnilai', compact('subEvents'));
    }

    public function usulan($subEventId)
    {
        if (Auth::user()->isAdminBapperida()) {
            return redirect()->route('inovasi.usulan-riwayat', $subEventId);
        }

        $subEvent = SubEvent::with('event', 'bidangs')->findOrFail($subEventId);
        $usulans  = Usulan::with('anggotaTim', 'bidang')
            ->where('user_id', Auth::id())
            ->where('sub_event_id', $subEventId)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('inovasi.usulan', compact('subEvent', 'usulans'));
    }

    public function rekapPendaftar($subEventId)
    {
        $subEvent     = SubEvent::with('event')->findOrFail($subEventId);
        $subEventNama = $subEvent->sub_event;

        $usulans    = Usulan::with('bidang')->where('sub_event_id', $subEventId)->orderBy('inovator')->get();
        $usulanIds  = $usulans->pluck('id')->toArray();

        $nilaiT1Rows = PenilaianUsulan::whereIn('usulan_id', $usulanIds)->get()->groupBy('usulan_id');
        $nilaiT2Rows = Pemenang::whereIn('usulan_id', $usulanIds)->get()->groupBy('usulan_id');

        $usulan = $usulans->map(function ($u) use ($nilaiT1Rows, $nilaiT2Rows) {
            $t1    = $this->hitungNilaiAkhir($nilaiT1Rows->get($u->id) ?? collect());
            $t2    = $this->hitungNilaiAkhir($nilaiT2Rows->get($u->id) ?? collect());
            $total = ($t1 !== '-' && $t2 !== '-')
                ? round(((float) $t1 + (float) $t2) / 2, 2)
                : ($t1 !== '-' ? $t1 : ($t2 !== '-' ? $t2 : '-'));

            return [
                'judul'        => $u->nama_inovasi ?: ($u->judul ?? '-'),
                'instansi'     => $u->inovator ?? '-',
                'link_youtube' => $u->link_video ?? '',
                'no_hp'        => $u->ketua_wa ?? '-',
                'kategori'     => $u->kategori ?? '-',
                'nilai_t1'     => $t1,
                'nilai_t2'     => $t2,
                'nilai_total'  => $total,
            ];
        })->values();

        return view('inovasi.rekap_pendaftar', compact('subEventNama', 'usulan'));
    }

    public function usulanRiwayat($subEventId)
    {
        $subEvent     = SubEvent::with('event')->findOrFail($subEventId);
        $subEventNama = $subEvent->sub_event;
        $eventNama    = $subEvent->event->nama_event ?? '-';
        $isAdmin      = Auth::user()->isAdminBapperida();

        $query = Usulan::with('bidang', 'anggotaTim', 'user')->where('sub_event_id', $subEventId);
        if ($isAdmin) {
            $query->submitted();
        } else {
            $query->where('user_id', Auth::id());
        }

        $usulan = $query->orderBy('updated_at', 'desc')->get();

        $sudahDinilaiIds = PenilaianUsulan::whereIn('usulan_id', $usulan->pluck('id'))
            ->distinct()->pluck('usulan_id')->toArray();

        $usulan->each(fn($u) => $u->sudah_dinilai = in_array($u->id, $sudahDinilaiIds));

        return view('inovasi.usulan_riwayat', compact('usulan', 'subEventNama', 'eventNama', 'isAdmin'));
    }

    public function usulanNilai($subEventId)
    {
        $subEvent     = SubEvent::with('event')->findOrFail($subEventId);
        $subEventNama = $subEvent->sub_event;
        $eventNama    = $subEvent->event->nama_event ?? '-';

        $usulan    = Usulan::where('user_id', Auth::id())->where('sub_event_id', $subEventId)->orderBy('updated_at', 'desc')->get();
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

    // ══════════════════════════════════════════════════════════════════════
    // CRUD USULAN
    // ══════════════════════════════════════════════════════════════════════

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sub_event_id'          => 'required|exists:sub_events,id',
            'bidang_id'             => 'required|exists:bidangs,id',
            'judul'                 => 'required|string|max:255',
            'inovator'              => 'required|string|max:255',
            'nama_inovasi'          => 'required|string|max:255',
            'interaksi'             => 'required|string|max:100',
            'nama_tim'              => 'nullable|string|max:255',
            'ketua_nama'            => 'required|string|max:255',
            'ketua_email'           => 'required|email|max:255',
            'ketua_wa'              => 'required|string|max:20',
            'alamat_ketua'          => 'required|string|max:255',
            'ktp'                   => 'required|string|max:50',
            'kategori'              => 'required|in:umum,pelajar',
            'asal_sekolah'          => 'required_if:kategori,pelajar|nullable|string|max:255',
            'nama_guru'             => 'nullable|string|max:150',
            // Narasi — nullable saat store (diisi bertahap per step)
            'latar_belakang'        => 'nullable|string',
            'kondisi_sebelumnya'    => 'nullable|string',
            'sasaran_tujuan'        => 'nullable|string',
            'materi_inovasi'        => 'nullable|string',
            'deskripsi'             => 'nullable|string',
            'bahan_baku'            => 'nullable|string',
            'cara_kerja'            => 'nullable|string',
            'keunggulan'            => 'nullable|string',
            'hasil_diharapkan'      => 'nullable|string',
            'manfaat'               => 'nullable|string',
            'rencana_berkelanjutan' => 'nullable|string',
            // File: validasi dasar Laravel (ekstensi + ukuran)
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

        // Validasi magic bytes + simpan dengan nama acak
        foreach ([
            'file_surat_pernyataan' => ['pdf','doc','docx'],
            'file_proposal'         => ['pdf','doc','docx'],
            'file_gambar'           => ['jpg','jpeg','png'],
        ] as $field => $exts) {
            $path = $this->handleFileUpload($request, $field, $exts, 2, $field);
            if ($path) {
                $data[$field] = $path;
            } else {
                unset($data[$field]);
            }
        }

        $usulan = Usulan::create($data);

        if (!empty($validated['anggota'])) {
            foreach ($validated['anggota'] as $nama) {
                if (!empty(trim($nama))) {
                    $usulan->anggotaTim()->create(['nama_anggota' => trim($nama)]);
                }
            }
        }

        Log::info('TAMBAH USULAN', ['user' => Auth::user()->email, 'usulan_id' => $usulan->id]);
        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil disimpan.',
            'usulan'  => $usulan->load('anggotaTim', 'bidang'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $usulan = Usulan::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if ($usulan->is_submitted) {
            return response()->json(['success' => false, 'message' => 'Usulan yang sudah dikirim tidak dapat diedit.'], 422);
        }

        $validated = $request->validate([
            'bidang_id'             => 'required|exists:bidangs,id',
            'judul'                 => 'required|string|max:255',
            'inovator'              => 'required|string|max:255',
            'nama_inovasi'          => 'required|string|max:255',
            'interaksi'             => 'required|string|max:100',
            'nama_tim'              => 'nullable|string|max:255',
            'ketua_nama'            => 'required|string|max:255',
            'ketua_email'           => 'required|email|max:255',
            'ketua_wa'              => 'required|string|max:20',
            'alamat_ketua'          => 'required|string|max:255',
            'ktp'                   => 'required|string|max:50',
            'kategori'              => 'required|in:umum,pelajar',
            'asal_sekolah'          => 'required_if:kategori,pelajar|nullable|string|max:255',
            'nama_guru'             => 'nullable|string|max:150',
            // Narasi — nullable saat update (diisi bertahap per step)
            'latar_belakang'        => 'nullable|string',
            'kondisi_sebelumnya'    => 'nullable|string',
            'sasaran_tujuan'        => 'nullable|string',
            'materi_inovasi'        => 'nullable|string',
            'deskripsi'             => 'nullable|string',
            'bahan_baku'            => 'nullable|string',
            'cara_kerja'            => 'nullable|string',
            'keunggulan'            => 'nullable|string',
            'hasil_diharapkan'      => 'nullable|string',
            'manfaat'               => 'nullable|string',
            'rencana_berkelanjutan' => 'nullable|string',
            'file_surat_pernyataan' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'file_proposal'         => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'file_gambar'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'link_video'            => 'nullable|url|max:255',
            'anggota'               => 'nullable|array',
            'anggota.*'             => 'nullable|string|max:255',
        ]);

        $data = $validated;
        unset($data['anggota']);

        // Validasi magic bytes + simpan dengan nama acak
        foreach ([
            'file_surat_pernyataan' => ['pdf','doc','docx'],
            'file_proposal'         => ['pdf','doc','docx'],
            'file_gambar'           => ['jpg','jpeg','png'],
        ] as $field => $exts) {
            $path = $this->handleFileUpload($request, $field, $exts, 2, $field, $usulan->$field);
            if ($path) {
                $data[$field] = $path;
            } else {
                unset($data[$field]);
            }
        }

        $usulan->update($data);

        $usulan->anggotaTim()->delete();
        if (!empty($validated['anggota'])) {
            foreach ($validated['anggota'] as $nama) {
                if (!empty(trim($nama))) {
                    $usulan->anggotaTim()->create(['nama_anggota' => trim($nama)]);
                }
            }
        }

        Log::info('EDIT USULAN', ['user' => Auth::user()->email, 'usulan_id' => $usulan->id]);
        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil diperbarui.',
            'usulan'  => $usulan->fresh()->load('anggotaTim', 'bidang'),
        ]);
    }

    public function destroy($id)
    {
        $usulan = Usulan::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if ($usulan->is_submitted) {
            return response()->json(['success' => false, 'message' => 'Usulan yang sudah dikirim tidak dapat dihapus.'], 422);
        }

        foreach (['file_surat_pernyataan', 'file_proposal', 'file_gambar'] as $field) {
            if ($usulan->$field) Storage::disk('public')->delete($usulan->$field);
        }

        // Disable foreign key checks untuk SQLite
        \DB::statement('PRAGMA foreign_keys = OFF');
        $usulan->catatanPenilai()->delete();
        $usulan->anggotaTim()->delete();
        $usulan->delete();
        \DB::statement('PRAGMA foreign_keys = ON');

        return response()->json(['success' => true, 'message' => 'Usulan berhasil dihapus.']);
    }

    public function kirim($id)
    {
        $usulan = Usulan::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if ($usulan->is_submitted) {
            return response()->json(['success' => false, 'message' => 'Usulan sudah pernah dikirim.'], 422);
        }

        // Validasi semua field wajib saat kirim
        $requiredFields = [
            'latar_belakang'        => 'Latar Belakang',
            'kondisi_sebelumnya'    => 'Kondisi Sebelumnya',
            'sasaran_tujuan'        => 'Sasaran dan Tujuan',
            'deskripsi'             => 'Deskripsi Inovasi',
            'cara_kerja'            => 'Cara Kerja',
            'keunggulan'            => 'Keunggulan',
            'hasil_diharapkan'      => 'Hasil yang Diharapkan',
            'manfaat'               => 'Manfaat',
            'rencana_berkelanjutan' => 'Rencana Berkelanjutan',
        ];

        $kosong = [];
        foreach ($requiredFields as $field => $label) {
            if (empty($usulan->$field)) {
                $kosong[] = $label;
            }
        }

        if (!empty($kosong)) {
            return response()->json([
                'success' => false,
                'message' => 'Lengkapi field berikut sebelum mengirim: ' . implode(', ', $kosong) . '.',
            ], 422);
        }

        $usulan->update(['is_submitted' => true, 'status' => 'Sedang Dinilai']);
        Log::info('KIRIM USULAN', ['user' => Auth::user()->email, 'usulan_id' => $usulan->id]);
        return response()->json(['success' => true, 'message' => 'Usulan berhasil dikirim!']);
    }

    public function editStatus(Request $request, $id)
    {
        if (!Auth::user()->isAdminBapperida()) abort(403, 'Akses ditolak.');

        $request->validate(['status' => 'required|in:Melengkapi Data,Sedang Dinilai,Selesai']);

        $usulan = Usulan::findOrFail($id);

        if ($request->status === 'Melengkapi Data') {
            if (PenilaianUsulan::where('usulan_id', $usulan->id)->exists()) {
                return response()->json(['success' => false, 'message' => 'Status tidak dapat dikembalikan karena sudah ada penilai yang memberikan nilai.'], 422);
            }
            $usulan->is_submitted = false;
        }

        $usulan->status = $request->status;
        $usulan->save();

        Log::info('EDIT STATUS USULAN', ['admin' => Auth::user()->email, 'usulan_id' => $usulan->id, 'status_baru' => $usulan->status]);
        return response()->json(['success' => true, 'message' => 'Status usulan berhasil diperbarui.', 'status' => $usulan->status]);
    }
}