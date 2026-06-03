<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penilai;

class PenilaiController extends Controller
{
    public function index()
    {
        $penilai = Penilai::orderBy('nama')->get();
        return view('master.penilai', compact('penilai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'  => 'required|string|max:255',
            'email' => 'required|email|unique:penilais,email',
        ]);

        Penilai::create([
            'nama'  => $request->nama,
            'email' => $request->email,
        ]);

        return redirect()->route('penilai.index')
                         ->with('success', 'Penilai berhasil ditambahkan');
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

        return redirect()->route('penilai.index')
                         ->with('success', 'Penilai berhasil diperbarui');
    }

    public function destroy($id)
    {
        Penilai::findOrFail($id)->delete();

        return redirect()->route('penilai.index')
                         ->with('success', 'Penilai berhasil dihapus');
    }
}