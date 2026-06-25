<?php

namespace App\Http\Controllers;

use App\Models\Penilai;
use App\Models\SubEvent;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PenilaiController extends Controller
{
        /* ══════════════════════════════════════════════════════════════
        |  INDEX  (Level 1)
        |  Menampilkan daftar semua SubEvent beserta jumlah penilai-nya.
        |  User memilih salah satu SubEvent untuk masuk ke Level 2 (detail).
        |
        |  Eager load:
        |    - event   : untuk menampilkan nama event induk di tabel
        |    - penilai : untuk menghitung jumlah penilai per sub event
        |                tanpa query tambahan di Blade
        ══════════════════════════════════════════════════════════════ */
    public function index()
    {
        $subEvents = SubEvent::with(['event', 'penilai'])
            ->orderBy('tahun', 'desc')
            ->get();

        return view('master.penilai', compact('subEvents'));
    }

        /* ══════════════════════════════════════════════════════════════
        |  DETAIL  (Level 2)
        |  Menampilkan daftar penilai untuk satu SubEvent tertentu.
        |  Sekaligus menyiapkan dropdown user untuk modal Tambah/Ubah.
        |
        |  $usersPenilai:
        |    Mengambil semua user dengan hak_akses = 'penilai', diurutkan
        |    by nama. Tidak difilter per sub event karena dropdown perlu
        |    menampilkan semua opsi — termasuk penilai yang sudah terdaftar
        |    agar modal Ubah bisa pre-fill user yang sedang aktif.
        |    Duplikasi dicegah di layer store/update, bukan di sini.
        |
        |  Hanya mengambil kolom id, nama, email — tidak perlu kolom lain.
        ══════════════════════════════════════════════════════════════ */
    public function detail(int $subEventId)
    {
        $subEvent = SubEvent::with('event')->findOrFail($subEventId);

        $penilai = Penilai::query()
            ->where('sub_event_id', $subEventId)
            ->orderBy('nama')
            ->get();

        $usersPenilai = User::query()
            ->where('hak_akses', 'penilai')
            ->orderBy('nama')
            ->get(['id', 'nama', 'email']);

        return view('master.penilai-detail', compact('subEvent', 'penilai', 'usersPenilai'));
    }

        /* ══════════════════════════════════════════════════════════════
        |  STORE
        |  Mendaftarkan penilai baru ke sub event tertentu.
        |  Dipanggil via AJAX (fetch) dari modal Tambah Penilai.
        |
        |  Validasi:
        |    - sub_event_id : wajib, harus exist di tabel sub_events
        |    - user_id      : wajib, harus exist di tabel users
        |
        |  Guard berlapis sebelum insert:
        |    1. Cek duplikat — user yang sama tidak boleh terdaftar dua
        |       kali di sub event yang sama
        |    2. Cek hak akses — hanya user dengan hak_akses = 'penilai'
        |       yang boleh didaftarkan, mencegah abuse dari request manual
        |
        |  nama & email disalin dari tabel users saat insert agar data
        |  penilai tetap terbaca meski user dihapus di kemudian hari.
        |
        |  Response sukses menyertakan update_url & destroy_url untuk
        |  tombol aksi di baris yang baru di-append ke DataTable.
        ══════════════════════════════════════════════════════════════ */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'sub_event_id' => 'required|exists:sub_events,id',
            'user_id'      => 'required|exists:users,id',
        ]);

        // Guard 1 — cek duplikat user di sub event yang sama
        $exists = Penilai::query()
            ->where('sub_event_id', $request->sub_event_id)
            ->where('user_id', $request->user_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'User ini sudah menjadi penilai di sub event ini.',
            ], 422);
        }

        $user = User::findOrFail($request->user_id);

        // Guard 2 — pastikan user benar-benar berhak sebagai penilai
        if ($user->hak_akses !== 'penilai') {
            return response()->json([
                'success' => false,
                'message' => 'User yang dipilih bukan memiliki hak akses Penilai.',
            ], 422);
        }

        // Salin nama & email dari user ke record penilai
        $penilai = Penilai::create([
            'sub_event_id' => $request->sub_event_id,
            'user_id'      => $user->id,
            'nama'         => $user->nama,
            'email'        => $user->email,
        ]);

        return response()->json([
            'success' => true,
            'penilai' => array_merge($penilai->toArray(), [
                'update_url'  => route('penilai.update', $penilai->id),
                'destroy_url' => route('penilai.destroy', $penilai->id),
            ]),
        ]);
    }

        /* ══════════════════════════════════════════════════════════════
        |  UPDATE
        |  Mengganti user yang menjadi penilai pada record yang sudah ada.
        |  Dipanggil via AJAX (fetch + _method: PUT) dari modal Ubah.
        |
        |  Validasi:
        |    - user_id : wajib, harus exist di tabel users
        |      (sub_event_id tidak bisa diganti — penilai tidak bisa
        |       dipindah ke sub event lain, hanya user-nya yang diganti)
        |
        |  Guard berlapis sebelum update:
        |    1. Cek duplikat — user baru tidak boleh sudah terdaftar di
        |       sub event yang sama, kecuali itu dirinya sendiri (where id != $id)
        |    2. Cek hak akses — sama seperti store
        |
        |  nama & email diperbarui mengikuti data user yang baru dipilih.
        |
        |  Response menyertakan objek penilai lengkap agar JS bisa
        |  memperbarui data-attribute tombol di baris tabel sekaligus.
        ══════════════════════════════════════════════════════════════ */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $penilai = Penilai::findOrFail($id);
        $user    = User::findOrFail($request->user_id);

        // Guard 1 — cek duplikat, kecualikan record ini sendiri
        $exists = Penilai::query()
            ->where('sub_event_id', $penilai->sub_event_id)
            ->where('user_id', $request->user_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'User ini sudah menjadi penilai di sub event ini.',
            ], 422);
        }

        // Guard 2 — pastikan user baru juga berhak sebagai penilai
        if ($user->hak_akses !== 'penilai') {
            return response()->json([
                'success' => false,
                'message' => 'User yang dipilih bukan memiliki hak akses Penilai.',
            ], 422);
        }

        $penilai->update([
            'user_id' => $user->id,
            'nama'    => $user->nama,
            'email'   => $user->email,
        ]);

        return response()->json([
            'success' => true,
            'penilai' => [
                'id'          => $penilai->id,
                'user_id'     => $penilai->user_id,
                'nama'        => $penilai->nama,
                'email'       => $penilai->email,
                'update_url'  => route('penilai.update', $penilai->id),
                'destroy_url' => route('penilai.destroy', $penilai->id),
            ],
        ]);
    }

        /* ══════════════════════════════════════════════════════════════
        |  DESTROY
        |  Menghapus penilai secara permanen dari database.
        |  Dipanggil via AJAX (fetch + _method: DELETE) dari modal Hapus.
        |
        |  Menghapus record Penilai tidak menghapus User-nya —
        |  keduanya adalah entitas terpisah. User tetap bisa didaftarkan
        |  kembali sebagai penilai di sub event lain.
        ══════════════════════════════════════════════════════════════ */
    public function destroy(int $id): JsonResponse
    {
        Penilai::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}