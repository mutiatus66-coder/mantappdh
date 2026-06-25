<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    /* ══════════════════════════════════════════
       HELPER: CEK HAK AKSES ADMIN
       Dipanggil di setiap method; abort 403
       jika user bukan admin Bapperida.
    ══════════════════════════════════════════ */
    private function authorizeAdmin(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || !$user->isAdminBapperida()) {
            abort(403, 'Akses ditolak.');
        }
    }

    /* ══════════════════════════════════════════
       HELPER: BUILD ARRAY RESPONSE PENGUMUMAN
       Mengembalikan data pengumuman beserta
       URL file, update, dan destroy agar bisa
       langsung dipakai JS (appendRow).
    ══════════════════════════════════════════ */
    private function pengumumanPayload(Pengumuman $pengumuman): array
    {
        return array_merge($pengumuman->toArray(), [
            'file_url'    => $pengumuman->file_path
                                ? asset('storage/' . $pengumuman->file_path)
                                : null,
            'update_url'  => route('pengumuman.update',  $pengumuman->id),
            'destroy_url' => route('pengumuman.destroy', $pengumuman->id),
        ]);
    }

    /* ══════════════════════════════════════════
       HELPER: SIMPAN FILE UPLOAD
       Menyimpan file ke disk 'public' di folder
       'pengumuman/', menghapus file lama jika ada.
       Mengembalikan path relatif yang tersimpan.
    ══════════════════════════════════════════ */
    private function storeFile(Request $request, ?string $oldPath = null): ?string
    {
        if (!$request->hasFile('file')) {
            return null; // Tidak ada file baru yang diupload
        }

        // Hapus file lama dari storage sebelum menyimpan yang baru
        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return $request->file('file')->store('pengumuman', 'public');
    }

    /* ══════════════════════════════════════════
       INDEX — Tampilkan halaman master pengumuman
       Diurutkan terbaru di atas (latest created_at)
    ══════════════════════════════════════════ */
    public function index()
    {
        $this->authorizeAdmin();

        $pengumuman = Pengumuman::latest('created_at')->get();

        return view('master.pengumuman', compact('pengumuman'));
    }

    /* ══════════════════════════════════════════
       STORE — Simpan pengumuman baru
       Menerima FormData (multipart) karena ada
       kemungkinan file upload.
    ══════════════════════════════════════════ */
    public function store(Request $request): JsonResponse
    {
        $this->authorizeAdmin();

        $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status'    => 'required|in:Published,Draft',
            'file'      => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp,svg|max:2048',
        ]);

        $data = $request->only('judul', 'deskripsi', 'status');

        // Simpan file jika ada
        $filePath = $this->storeFile($request);
        if ($filePath) {
            $data['file_path'] = $filePath;
        }

        $pengumuman = Pengumuman::create($data);

        return response()->json([
            'success'    => true,
            'pengumuman' => $this->pengumumanPayload($pengumuman),
        ]);
    }

    /* ══════════════════════════════════════════
       UPDATE — Perbarui pengumuman yang ada
       File lama dihapus otomatis jika ada file
       baru yang diupload.
    ══════════════════════════════════════════ */
    public function update(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin();

        $pengumuman = Pengumuman::findOrFail($id);

        $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status'    => 'required|in:Published,Draft',
            'file'      => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp,svg|max:2048',
        ]);

        $data = $request->only('judul', 'deskripsi', 'status');

        // Ganti file jika ada upload baru (hapus file lama otomatis)
        $filePath = $this->storeFile($request, $pengumuman->file_path);
        if ($filePath) {
            $data['file_path'] = $filePath;
        }

        $pengumuman->update($data);
        $pengumuman->refresh();

        return response()->json([
            'success'  => true,
            'file_url' => $pengumuman->file_path
                            ? asset('storage/' . $pengumuman->file_path)
                            : null,
        ]);
    }

    /* ══════════════════════════════════════════
       DESTROY — Hapus pengumuman beserta file-nya
       File di storage dihapus terlebih dahulu
       sebelum record dihapus dari database.
    ══════════════════════════════════════════ */
    public function destroy(int $id): JsonResponse
    {
        $this->authorizeAdmin();

        $pengumuman = Pengumuman::findOrFail($id);

        // Hapus file terkait dari storage jika ada
        if ($pengumuman->file_path) {
            Storage::disk('public')->delete($pengumuman->file_path);
        }

        $pengumuman->delete();

        return response()->json(['success' => true]);
    }
}