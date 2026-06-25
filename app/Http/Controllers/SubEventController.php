<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\SubEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubEventController extends Controller
{
        /* ══════════════════════════════════════════════════════════════
        |  INDEX
        |  Menampilkan halaman Master Sub Event.
        |  Mengambil semua SubEvent beserta relasi Event-nya (eager load)
        |  agar kolom "Nama Event" bisa ditampilkan tanpa N+1 query.
        |  Diurutkan descending by tahun agar entri terbaru muncul duluan.
        |
        |  $events dikirim terpisah untuk mengisi dropdown pilih Event
        |  di modal Tambah / Ubah Sub Event.
        ══════════════════════════════════════════════════════════════ */
    public function index()
    {
        $subEvents = SubEvent::with('event')
            ->orderBy('tahun', 'desc')
            ->get();

        /* ══════════════════════════════════════════════════════════════
        |  Di bawah ini kadang error tapi tenang, ini cuma masalah IDE nya
        |  fungsinya tetep jalan.
        - Regan
        ══════════════════════════════════════════════════════════════ */
        $events = Event::orderBy('nama_event')->get();

        return view('master.sub-event', compact('subEvents', 'events'));
    }

    /* ══════════════════════════════════════════════════════════════
     |  STORE
     |  Menyimpan sub event baru ke database.
     |  Dipanggil via AJAX (fetch) dari modal Tambah Sub Event.
     |
     |  Validasi:
     |    - event_id  : wajib, integer, harus exist di tabel events
     |    - tahun     : wajib, tepat 4 digit angka
     |    - sub_event : wajib, string, maks 255 karakter
     |    - mulai     : wajib, format tanggal valid
     |    - berakhir  : wajib, tanggal valid, harus >= mulai
     |
     |  Field opsional:
     |    - kategori  : default 'SEMUA BIDANG' jika tidak dikirim
     |
     |  Response sukses menyertakan field tambahan untuk JS:
     |    - event_nama  : nama event induk, untuk kolom Event di tabel
     |    - update_url  : URL route sub-event.update untuk tombol Ubah
     |    - destroy_url : URL route sub-event.destroy untuk tombol Hapus
     ══════════════════════════════════════════════════════════════ */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'event_id'  => 'required|integer|exists:events,id',
            'tahun'     => 'required|digits:4',
            'sub_event' => 'required|string|max:255',
            'mulai'     => 'required|date',
            'berakhir'  => 'required|date|after_or_equal:mulai',
        ]);

        $subEvent = SubEvent::create([
            'event_id'  => $request->event_id,
            'tahun'     => $request->tahun,
            'sub_event' => $request->sub_event,
            'kategori'  => $request->kategori ?? 'SEMUA BIDANG',
            'mulai'     => $request->mulai,
            'berakhir'  => $request->berakhir,
        ]);

        // Load relasi event agar bisa ambil nama_event untuk response JS
        $subEvent->load('event');

        return response()->json([
            'success'  => true,
            'subEvent' => array_merge($subEvent->toArray(), [
                'event_nama'  => $subEvent->event->nama_event ?? '-',
                'update_url'  => route('sub-event.update', $subEvent->id),
                'destroy_url' => route('sub-event.destroy', $subEvent->id),
            ]),
        ]);
    }

    /* ══════════════════════════════════════════════════════════════
     |  UPDATE
     |  Memperbarui data sub event yang sudah ada.
     |  Dipanggil via AJAX (fetch + _method: PUT) dari modal Ubah.
     |
     |  Validasi sama persis dengan store — semua field wajib dikirim
     |  ulang karena ini full-update (bukan partial/PATCH).
     |
     |  event_id boleh diganti — sub event bisa dipindah ke event lain.
     |  kategori tetap opsional dan fallback ke 'SEMUA BIDANG'.
     |
     |  Response menyertakan event_nama agar JS bisa memperbarui
     |  kolom Event di baris tabel tanpa reload halaman.
     ══════════════════════════════════════════════════════════════ */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'event_id'  => 'required|integer|exists:events,id',
            'tahun'     => 'required|digits:4',
            'sub_event' => 'required|string|max:255',
            'mulai'     => 'required|date',
            'berakhir'  => 'required|date|after_or_equal:mulai',
        ]);

        $subEvent = SubEvent::findOrFail($id);

        $subEvent->update([
            'event_id'  => $request->event_id,
            'tahun'     => $request->tahun,
            'sub_event' => $request->sub_event,
            'kategori'  => $request->kategori ?? 'SEMUA BIDANG',
            'mulai'     => $request->mulai,
            'berakhir'  => $request->berakhir,
        ]);

        // Reload relasi setelah update agar event_nama akurat
        // (event_id bisa saja berubah dari sebelumnya)
        $subEvent->load('event');

        return response()->json([
            'success'    => true,
            'event_nama' => $subEvent->event->nama_event ?? '-',
        ]);
    }

    /* ══════════════════════════════════════════════════════════════
     |  DESTROY
     |  Menghapus sub event secara permanen dari database.
     |  Dipanggil via AJAX (fetch + _method: DELETE) dari modal Hapus.
     |
     |  Perhatian: jika ada relasi Bidang, Penilai, atau Usulan yang
     |  terikat ke sub event ini, pastikan migration sudah mengatur
     |  onDelete('cascade') atau hapus manual sebelum destroy,
     |  agar tidak terjadi orphan record.
     ══════════════════════════════════════════════════════════════ */
    public function destroy(int $id): JsonResponse
    {
        SubEvent::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}