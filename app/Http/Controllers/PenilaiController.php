<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Penilai;

class PenilaiController extends Controller
{
    public function index()
    {
        $penilai = Penilai::orderBy('nama', 'asc')->get();
        return view('master.penilai', compact('penilai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'  => 'required|string|max:255',
            'email' => 'required|email|unique:penilais,email',
        ]);

        $penilai = Penilai::create([
            'nama'  => $request->nama,
            'email' => $request->email,
        ]);

        return response()->json([
            'success' => true,
            'penilai' => array_merge($penilai->toArray(), [
                'update_url'  => route('penilai.update', $penilai->id),
                'destroy_url' => route('penilai.destroy', $penilai->id),
            ]),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'  => 'required|string|max:255',
            'email' => 'required|email|unique:penilais,email,' . $id,
        ]);

        Penilai::findOrFail($id)->update([
            'nama'  => $request->nama,
            'email' => $request->email,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Penilai::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}