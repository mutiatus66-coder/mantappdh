<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SubEvent;
use App\Models\Usulan;

class InovasiController extends Controller
{
    // ── Halaman daftar sub event (pintu masuk untuk peserta) ──────────────

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

    // ── Form tambah / edit usulan (milik peserta) ─────────────────────────

   public function usulan($subEventId)
{
    $subEvent  = SubEvent::with('event', 'bidangs')->findOrFail($subEventId);
    $sub_event = $subEvent->sub_event;

    $usulan = Usulan::where('sub_event_id', $subEventId)
        ->get()
        ->map(function ($u) {
            return [
                'judul'        => $u->judul ?? '',
                'instansi'     => $u->inovator ?? '',
                'link_youtube' => $u->link_youtube ?? '',
                'no_hp'        => $u->ketua_wa ?? '',
                'kategori'     => $u->kategori ?? '',
                'nilai_t1'     => $u->nilai_t1 ?? '-',
                'nilai_t2'     => $u->nilai_t2 ?? '-',
                'nilai_total'  => $u->nilai_total ?? '-',
            ];
        });

    return view('inovasi.usulan', compact('sub_event', 'usulan'));
}

    // ── Rekap semua pendaftar per sub event (admin) ───────────────────────

    public function rekapPendaftar($subEventId)
    {
        $subEvent  = SubEvent::with('event')->findOrFail($subEventId);
        $sub_event = $subEvent->sub_event;

        $usulan = Usulan::where('sub_event_id', $subEventId)
            ->get()
            ->map(function ($u) {
                return [
                    'judul'        => $u->judul ?? '',
                    'instansi'     => $u->inovator ?? '',
                    'link_youtube' => $u->link_youtube ?? '',
                    'no_hp'        => $u->ketua_wa ?? '',
                    'kategori'     => $u->kategori ?? '',
                    'nilai_t1'     => $u->nilai_t1 ?? '-',
                    'nilai_t2'     => $u->nilai_t2 ?? '-',
                    'nilai_total'  => $u->nilai_total ?? '-',
                ];
            });

        return view('inovasi.rekap_pendaftar', compact('sub_event', 'usulan'));
    }

    // ── Riwayat usulan milik peserta ──────────────────────────────────────

    public function usulanRiwayat($subEventId)
    {
        $subEvent     = SubEvent::with('event')->findOrFail($subEventId);
        $subEventNama = $subEvent->sub_event;
        $eventNama    = $subEvent->event->nama_event ?? '-';

        $usulan = Usulan::where('user_id', Auth::id())
            ->where('sub_event_id', $subEventId)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('inovasi.usulan_riwayat', compact('usulan', 'subEventNama', 'eventNama'));
    }

    // ── Rekap nilai usulan milik peserta ──────────────────────────────────

    public function usulanNilai($subEventId)
    {
        $subEvent     = SubEvent::with('event')->findOrFail($subEventId);
        $subEventNama = $subEvent->sub_event;
        $eventNama    = $subEvent->event->nama_event ?? '-';

        $usulan = Usulan::where('user_id', Auth::id())
            ->where('sub_event_id', $subEventId)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('inovasi.usulan_nilai', compact('usulan', 'subEventNama', 'eventNama'));
    }

    // ── CRUD Usulan ───────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sub_event_id' => 'required|exists:sub_events,id',
            'judul'        => 'required|string|max:255',
            'inovator'     => 'required|string|max:255',
            'nama_inovasi' => 'required|string|max:255',
            'nama_tim'     => 'nullable|string|max:255',
            'ketua_nama'   => 'required|string|max:255',
            'ketua_email'  => 'required|email|max:255',
            'ketua_wa'     => 'nullable|string|max:20',
            'kategori'     => 'required|in:umum,pelajar',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status']  = 'Melengkapi Data';

        $usulan = Usulan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil disimpan.',
            'usulan'  => $usulan,
        ]);
    }

    public function update(Request $request, $id)
    {
        $usulan = Usulan::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($usulan->is_submitted) {
            return response()->json([
                'success' => false,
                'message' => 'Usulan yang sudah dikirim tidak dapat diedit.',
            ], 422);
        }

        $validated = $request->validate([
            'judul'        => 'required|string|max:255',
            'inovator'     => 'required|string|max:255',
            'nama_inovasi' => 'required|string|max:255',
            'nama_tim'     => 'nullable|string|max:255',
            'ketua_nama'   => 'required|string|max:255',
            'ketua_email'  => 'required|email|max:255',
            'ketua_wa'     => 'nullable|string|max:20',
            'kategori'     => 'required|in:umum,pelajar',
        ]);

        $usulan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil diperbarui.',
            'usulan'  => $usulan->fresh(),
        ]);
    }

    public function destroy($id)
    {
        $usulan = Usulan::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($usulan->is_submitted) {
            return response()->json([
                'success' => false,
                'message' => 'Usulan yang sudah dikirim tidak dapat dihapus.',
            ], 422);
        }

        $usulan->delete();

        return response()->json(['success' => true, 'message' => 'Usulan berhasil dihapus.']);
    }

    public function kirim($id)
    {
        $usulan = Usulan::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($usulan->is_submitted) {
            return response()->json([
                'success' => false,
                'message' => 'Usulan sudah pernah dikirim.',
            ], 422);
        }

        $usulan->update([
            'is_submitted' => true,
            'status'       => 'Sedang Dinilai',
        ]);

        return response()->json(['success' => true, 'message' => 'Usulan berhasil dikirim.']);
    }
}