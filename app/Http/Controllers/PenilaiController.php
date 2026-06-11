<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penilai;
use App\Models\SubEvent;

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
        $penilai  = Penilai::query()->where('sub_event_id', $subEventId)->orderBy('nama')->get();
        return view('master.penilai-detail', compact('subEvent', 'penilai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sub_event_id' => 'required|exists:sub_events,id',
            'nama'         => 'required|string|max:255',
            'email'        => 'required|email|unique:penilai,email',
        ]);

        $penilai = Penilai::create([
            'sub_event_id' => $request->sub_event_id,
            'nama'         => $request->nama,
            'email'        => $request->email,
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
            'nama'  => 'required|string|max:255',
            'email' => 'required|email|unique:penilai,email,' . $id,
        ]);

        Penilai::findOrFail($id)->update([
            'nama'  => $request->nama,
            'email' => $request->email,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(int $id)
    {
        Penilai::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}