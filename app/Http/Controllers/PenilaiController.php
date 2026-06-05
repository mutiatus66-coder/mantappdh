<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class PenilaiController extends Controller
{
    public function index()
    {
        $penilai = User::where('hak_akses', 'penilai')->orderBy('nama')->get();
        return view('master.penilai', compact('penilai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        User::create([
            'nama'      => $request->nama,
            'name'      => $request->nama,
            'email'     => $request->email,
            'hak_akses' => 'penilai',
            'status'    => 'active',
            'password'  => bcrypt('password123'), // password default
        ]);

        return redirect()->route('penilai.index')
                         ->with('success', 'Penilai berhasil ditambahkan. Password default: password123');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        User::findOrFail($id)->update([
            'nama' => $request->nama,
            'name' => $request->nama,
            'email' => $request->email,
        ]);

        return redirect()->route('penilai.index')
                         ->with('success', 'Penilai berhasil diperbarui');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return redirect()->route('penilai.index')
                         ->with('success', 'Penilai berhasil dihapus');
    }
}