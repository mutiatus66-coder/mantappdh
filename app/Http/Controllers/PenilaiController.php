<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penilai;
use App\Models\SubEvent;
use App\Models\User;

class PenilaiController extends Controller
{
    // Level 1 — daftar sub event
    public function index()
    {
        $subEvents = SubEvent::with(['event', 'penilai'])->orderBy('tahun', 'desc')->get();
        return view('master.penilai', compact('subEvents'));
    }

    // Level 2 — daftar penilai per sub event
    public function detail(int $subEventId)
    {
        $subEvent = SubEvent::with('event')->findOrFail($subEventId);
        $penilai  = Penilai::where('sub_event_id', $subEventId)->orderBy('nama')->get();

        // ═══ AMBIL USER DENGAN ROLE PENILAI ═══
        // Hanya user yang belum terdaftar di sub event ini yang akan muncul di dropdown (kecuali untuk edit nanti)
        $usersPenilai = User::where('hak_akses', 'penilai')
            ->orderBy('nama')
            ->get(['id', 'nama', 'email']);

        return view('master.penilai-detail', compact('subEvent', 'penilai', 'usersPenilai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sub_event_id' => 'required|exists:sub_events,id',
            'user_id'      => 'required|exists:users,id',
        ]);

        // 1. Cek apakah user sudah terdaftar di sub event ini
        $exists = Penilai::where('sub_event_id', $request->sub_event_id)
                         ->where('user_id', $request->user_id)
                         ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'User ini sudah menjadi penilai di sub event ini.'
            ], 422);
        }

        $user = User::findOrFail($request->user_id);

        // 2. Pastikan user benar-benar role penilai
        if ($user->hak_akses !== 'penilai') {
            return response()->json([
                'success' => false,
                'message' => 'User yang dipilih bukan memiliki hak akses Penilai.'
            ], 422);
        }

        // 3. Simpan data (nama & email diambil otomatis dari tabel users)
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

    public function update(Request $request, int $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $penilai = Penilai::findOrFail($id);
        $user = User::findOrFail($request->user_id);

        // Cek duplikasi (kecuali dirinya sendiri)
        $exists = Penilai::where('sub_event_id', $penilai->sub_event_id)
                         ->where('user_id', $request->user_id)
                         ->where('id', '!=', $id)
                         ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'User ini sudah menjadi penilai di sub event ini.'
            ], 422);
        }

        if ($user->hak_akses !== 'penilai') {
            return response()->json([
                'success' => false,
                'message' => 'User yang dipilih bukan memiliki hak akses Penilai.'
            ], 422);
        }

        // Update dengan data terbaru dari user
        $penilai->update([
            'user_id' => $user->id,
            'nama'    => $user->nama,
            'email'   => $user->email,
        ]);

        return response()->json([
            'success' => true,
            'penilai' => [
                'id'       => $penilai->id,
                'nama'     => $penilai->nama,
                'email'    => $penilai->email,
                'user_id'  => $penilai->user_id,
                'update_url'  => route('penilai.update', $penilai->id),
                'destroy_url' => route('penilai.destroy', $penilai->id),
            ],
        ]);
    }

    public function destroy(int $id)
    {
        Penilai::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
