<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenilaiController extends Controller
{
    public function index()
    {
        $penilai = [
            ['id' => 1, 'nama' => 'muhammad noor majid', 'email' => 'm.noormajid12@gmail.com'],
            ['id' => 2, 'nama' => 'Moch Nurrudin', 'email' => 'moch.nurrudin72@gmail.com'],
            ['id' => 3, 'nama' => 'Mujiono', 'email' => 'mujiono.aldifa@gmail.com'],
            ['id' => 4, 'nama' => 'Alam Surya', 'email' => 'alam.endriharto@gmail.com'],
            ['id' => 5, 'nama' => 'Eko Adri', 'email' => 'remingtonsteel320@yahoo.com'],
            ['id' => 6, 'nama' => 'Jatmiko', 'email' => 'okimfh99@gmail.com'],
        ];

        // Pilihan 2: Query Database Langsung (aktifkan jika tabel sudah ada)
        // $penilai = DB::table('penilais')
        //             ->select('id', 'nama', 'email')
        //             ->latest()
        //             ->paginate(15);

        return view('master.penilai', compact('penilai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'  => 'required|string|max:255',
            'email' => 'required|email|unique:penilais,email',
        ]);

        // Simpan menggunakan Query Builder
        DB::table('penilais')->insert([
            'nama'       => $request->nama,
            'email'      => $request->email,
            'created_at' => now(),
            'updated_at' => now(),
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

        DB::table('penilais')
            ->where('id', $id)
            ->update([
                'nama'       => $request->nama,
                'email'      => $request->email,
                'updated_at' => now(),
            ]);

        return redirect()->route('penilai.index')
                         ->with('success', 'Penilai berhasil diperbarui');
    }

    public function destroy($id)
    {
        DB::table('penilais')->where('id', $id)->delete();

        return redirect()->route('penilai.index')
                         ->with('success', 'Penilai berhasil dihapus');
    }
}
