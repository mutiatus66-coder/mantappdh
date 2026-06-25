<?php

namespace App\Http\Controllers;

use App\Models\Usulan;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
        /* ══════════════════════════════════════════════════════════════
        |  INDEX
        |  Menampilkan halaman Master User.
        |  Mengambil semua user tanpa filter — semua role tampil
        |  di tabel dan bisa dikelola dari sini.
        ══════════════════════════════════════════════════════════════ */
        /* ══════════════════════════════════════════════════════════════
        |  di bawah ini kadang error tapi tenang, ini cuma masalah IDE nya
        |  fungsinya tetep jalan.
        - Regan
        ══════════════════════════════════════════════════════════════ */
    public function index()
    {
        $users = User::orderBy('nama')->get();

        return view('master.user', compact('users'));
    }

        /* ══════════════════════════════════════════════════════════════
        |  STORE
        |  Membuat user baru.
        |  Dipanggil via AJAX (fetch) dari modal Tambah User.
        |
        |  Validasi:
        |    - nama      : wajib, string, maks 255
        |    - email     : wajib, format email, unik di tabel users
        |    - hak_akses : wajib, hanya nilai yang terdaftar di enum
        |    - status    : wajib, 'aktif' atau 'nonaktif'
        |    - password  : wajib untuk user baru, minimal 6 karakter
        |
        |  Catatan: kolom `name` diisi sama dengan `nama` karena Laravel
        |  Fortify / Sanctum menggunakan kolom `name` secara default.
        |
        |  Response sukses menyertakan URL aksi untuk tombol di baris
        |  baru yang di-append ke DataTable tanpa reload halaman.
        ══════════════════════════════════════════════════════════════ */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nama'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'hak_akses' => 'required|in:admin_bapperida,admin_kecamatan,admin_opd,peserta,penilai',
            'status'    => 'required|in:aktif,nonaktif',
            'password'  => 'required|min:6',
        ]);

        $user = User::create([
            'nama'      => $request->nama,
            'name'      => $request->nama,   // alias untuk kompatibilitas Fortify
            'email'     => $request->email,
            'hak_akses' => $request->hak_akses,
            'status'    => $request->status,
            'password'  => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'user'    => array_merge($user->toArray(), [
                'update_url'  => route('user.update', $user->id),
                'destroy_url' => route('user.destroy', $user->id),
                'login_url'   => route('user.login-as', $user->id),
            ]),
        ]);
    }

        /* ══════════════════════════════════════════════════════════════
        |  UPDATE
        |  Memperbarui data user yang sudah ada.
        |  Dipanggil via AJAX (fetch + _method: PUT) dari modal Ubah.
        |
        |  Validasi:
        |    - email     : unique dikecualikan untuk id diri sendiri
        |                  agar update tanpa ganti email tetap lolos
        |    - hak_akses : enum yang sama dengan store
        |    - password  : nullable — kosongkan jika tidak ingin diubah
        |
        |  Password hanya di-hash dan di-update jika field terisi
        |  (request->filled() mengecek tidak null dan tidak string kosong).
        ══════════════════════════════════════════════════════════════ */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $id,
            'hak_akses' => 'required|in:admin_bapperida,admin_kecamatan,admin_opd,peserta,penilai',
            'status'    => 'required|in:aktif,nonaktif',
            'password'  => 'nullable|min:6',
        ]);

        $data = [
            'nama'      => $request->nama,
            'name'      => $request->nama,
            'email'     => $request->email,
            'hak_akses' => $request->hak_akses,
            'status'    => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json(['success' => true]);
    }

        /* ══════════════════════════════════════════════════════════════
        |  DESTROY
        |  Menghapus user secara permanen dari database.
        |  Dipanggil via AJAX (fetch + _method: DELETE) dari modal Hapus.
        |
        |  Guard sebelum hapus:
        |    Jika user sudah memiliki data Usulan, hapus ditolak dengan
        |    pesan saran untuk menonaktifkan saja. Ini mencegah orphan
        |    record di tabel usulan yang masih mereferensikan user_id ini.
        |
        |  Tombol Hapus di Blade sudah tidak ditampilkan untuk akun
        |  yang sedang login (Auth::id() === $item->id), tapi guard
        |  di controller ini tetap diperlukan sebagai lapisan kedua
        |  jika request dikirim manual.
        ══════════════════════════════════════════════════════════════ */
    public function destroy(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Tolak hapus jika user sudah punya data usulan
        $punyaUsulan = Usulan::query()->where('user_id', $id)->exists();
        if ($punyaUsulan) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak dapat dihapus karena sudah memiliki data usulan. Nonaktifkan saja.',
            ], 422);
        }

        $user->delete();

        return response()->json(['success' => true]);
    }

        /* ══════════════════════════════════════════════════════════════
        |  LOGIN AS
        |  Memungkinkan admin masuk sebagai user lain tanpa password.
        |  Berguna untuk debug dan verifikasi tampilan per role.
        |
        |  Alur:
        |    1. ID admin asli disimpan di session (hanya sekali —
        |       jika sudah ada sesi login-as sebelumnya, tidak ditimpa)
        |    2. Auth::login() mengganti sesi aktif ke user target
        |    3. Redirect ke dashboard utama dengan flash info role aktif
        |
        |  Sesi `admin_original_id` digunakan oleh loginBack() untuk
        |  kembali ke akun admin semula.
        ══════════════════════════════════════════════════════════════ */
    public function loginAs(int $id): RedirectResponse
    {
        // Simpan ID admin asli hanya jika belum ada sesi login-as
        if (!session()->has('admin_original_id')) {
            session(['admin_original_id' => Auth::id()]);
        }

        $user = User::findOrFail($id);
        Auth::login($user);

        return redirect()->route('index')
            ->with('success', 'Sedang login sebagai ' . $user->nama . ' (' . $user->hak_akses . ')');
    }

        /* ══════════════════════════════════════════════════════════════
        |  LOGIN BACK
        |  Mengembalikan sesi ke akun admin semula setelah login-as.
        |
        |  Alur:
        |    1. Ambil `admin_original_id` dari session
        |    2. Login kembali ke akun admin tersebut
        |    3. Hapus `admin_original_id` dari session agar bersih
        |    4. Redirect ke halaman user dengan flash konfirmasi
        |
        |  Jika session tidak ditemukan (misalnya expired atau tidak
        |  pernah login-as), redirect ke dashboard dengan pesan error.
        ══════════════════════════════════════════════════════════════ */
    public function loginBack(): RedirectResponse
    {
        $originalId = session('admin_original_id');

        if (!$originalId) {
            return redirect()->route('index')
                ->with('error', 'Tidak ada sesi admin yang tersimpan.');
        }

        $admin = User::findOrFail($originalId);
        Auth::login($admin);

        session()->forget('admin_original_id');

        return redirect()->route('user.index')
            ->with('success', 'Kembali ke akun ' . $admin->nama);
    }
}