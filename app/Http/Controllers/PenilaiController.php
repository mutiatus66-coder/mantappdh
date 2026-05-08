<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenilaiController extends Controller
{
    public function index()
    {
        // Data dummy (nanti ganti dengan query dari database)
        $penilai = [
            ['id' => 1, 'nama' => 'muhammad noor majid', 'email' => 'm.noormajid12@gmail.com'],
            ['id' => 2, 'nama' => 'Moch Nurrudin', 'email' => 'moch.nurrudin72@gmail.com'],
            ['id' => 3, 'nama' => 'Mujiono', 'email' => 'mujiono.aldifa@gmail.com'],
            ['id' => 4, 'nama' => 'Alam Surya', 'email' => 'alam.endriharto@gmail.com'],
            ['id' => 5, 'nama' => 'Eko Adri', 'email' => 'remingtonsteel320@yahoo.com'],
            ['id' => 6, 'nama' => 'Jatmiko', 'email' => 'okimfh99@gmail.com'],
        ];

        return view('master.penilai', compact('penilai'));
    }

    public function store(Request $request)
    {
        // validasi & simpan
        // redirect back with success
        return redirect()->route('penilai.index')->with('success', 'Penilai berhasil ditambahkan');
    }

    public function destroy($id)
    {
        // hapus berdasarkan id
        return redirect()->route('penilai.index')->with('success', 'Penilai dihapus');
    }
}
