<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BidangController extends Controller
{
    /* ══════════════════════════════════════════════════════════════
     |  INDEX
     |  Menampilkan halaman Master Bidang.
     |  Mengambil semua Event yang memiliki minimal satu SubEvent,
     |  beserta relasi SubEvent → Bidang untuk di-loop di Blade.
     ══════════════════════════════════════════════════════════════ */
    public function index()
    {
        $events = Event::with(['subEvents.bidangs'])
            ->whereHas('subEvents')
            ->orderBy('nama_event')
            ->get();

        return view('master.bidang', compact('events'));
    }

    /* ══════════════════════════════════════════════════════════════
     |  STORE
     |  Menyimpan bidang baru ke database.
     |  Dipanggil via AJAX (fetch) dari modal Tambah Bidang.
     |
     |  Validasi:
     |    - sub_event_id  : wajib ada, harus exist di tabel sub_events
     |    - nama          : wajib, string, maks 255 karakter
     |    - status        : wajib, hanya boleh 'aktif' atau 'tidak_aktif'
     |
     |  Cek duplikat: nama yang sama (case-insensitive) di sub event
     |  yang sama tidak boleh ada dua kali.
     |
     |  Response sukses menyertakan field tambahan:
     |    - update_url    : URL route bidang.update untuk tombol Ubah
     |    - destroy_url   : URL route bidang.destroy untuk tombol Hapus
     |    - sub_event_nama: nama sub event, dibutuhkan Blade JS agar
     |                      tombol Ubah di baris baru bisa mengisi
     |                      label "Sub Event" di modal dengan benar
     ══════════════════════════════════════════════════════════════ */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'sub_event_id' => 'required|exists:sub_events,id',
            'nama'         => 'required|string|max:255',
            'status'       => 'required|in:aktif,tidak_aktif',
        ]);

        // Cek duplikat nama (case-insensitive) dalam sub event yang sama
        $exists = Bidang::query()
            ->where('sub_event_id', $request->sub_event_id)
            ->whereRaw('LOWER(nama) = ?', [strtolower($request->nama)])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Bidang dengan nama yang sama sudah ada pada Sub Event ini.',
            ]);
        }

        $bidang = Bidang::create($request->only('sub_event_id', 'nama', 'status'));

        // Load relasi subEvent agar bisa ambil nama sub event
        $bidang->load('subEvent');

        return response()->json([
            'success' => true,
            'bidang'  => array_merge($bidang->toArray(), [
                'update_url'     => route('bidang.update', $bidang->id),
                'destroy_url'    => route('bidang.destroy', $bidang->id),
                // Dibutuhkan JS Blade untuk mengisi data-sub-event-nama
                // pada tombol Ubah di baris yang baru di-append ke DataTable
                'sub_event_nama' => $bidang->subEvent?->sub_event ?? '',
            ]),
        ]);
    }

    /* ══════════════════════════════════════════════════════════════
     |  EDIT
     |  Mengembalikan data bidang sebagai JSON.
     |  Saat ini tidak dipakai secara langsung di frontend karena
     |  data sudah di-embed di data-attribute tombol Ubah.
     |  Dipertahankan sebagai fallback / keperluan API lain.
     ══════════════════════════════════════════════════════════════ */
    public function edit(Bidang $bidang): JsonResponse
    {
        return response()->json($bidang);
    }

    /* ══════════════════════════════════════════════════════════════
     |  UPDATE
     |  Memperbarui nama dan status bidang yang sudah ada.
     |  Dipanggil via AJAX (fetch + _method: PUT) dari modal Ubah Bidang.
     |
     |  Validasi:
     |    - nama   : wajib, string, maks 255 karakter
     |    - status : wajib, hanya boleh 'aktif' atau 'tidak_aktif'
     |
     |  sub_event_id tidak di-update — bidang tidak bisa dipindah
     |  ke sub event lain; diambil dari record yang sudah ada.
     |
     |  Cek duplikat mengecualikan record diri sendiri (where id != $id)
     |  agar update tanpa ganti nama tetap lolos validasi.
     ══════════════════════════════════════════════════════════════ */
    public function update(Request $request, int $id): JsonResponse
    {
        $bidang = Bidang::findOrFail($id);

        $request->validate([
            'nama'   => 'required|string|max:255',
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        // Cek duplikat nama dalam sub event yang sama, kecuali record ini sendiri
        $exists = Bidang::query()
            ->where('sub_event_id', $bidang->sub_event_id)
            ->whereRaw('LOWER(nama) = ?', [strtolower($request->nama)])
            ->where('id', '!=', $bidang->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Nama bidang sudah digunakan di sub event ini.',
            ]);
        }

        $bidang->update([
            'nama'   => $request->nama,
            'status' => $request->status,
        ]);

        return response()->json(['success' => true]);
    }

    /* ══════════════════════════════════════════════════════════════
     |  DESTROY
     |  Menghapus bidang secara permanen dari database.
     |  Dipanggil via AJAX (fetch + _method: DELETE) dari modal Hapus.
     |  Menggunakan findOrFail agar otomatis 404 jika id tidak ada.
     ══════════════════════════════════════════════════════════════ */
    public function destroy(int $id): JsonResponse
    {
        Bidang::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}