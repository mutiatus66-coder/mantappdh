<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    public function index()
    {
        // Data dummy (nanti ganti dengan database)
        $pengumuman = [
            [
                'id'          => 1,
                'judul'       => 'POSTER INOTEK AWARD 2023',
                'deskripsi'   => 'Pengumuman lomba poster INOTEK 2023',
                'status'      => 'Published',
                'file_path'   => null,
                'created_at'  => '2025-01-10'
            ],
            [
                'id'          => 2,
                'judul'       => 'Instruksi Bupati Magetan tentang Gerakan Magetan Berinovasi',
                'deskripsi'   => 'Instruksi Bupati untuk percepatan inovasi daerah',
                'status'      => 'Published',
                'file_path'   => null,
                'created_at'  => '2025-02-15'
            ],
        ];

        return view('master.pengumuman', compact('pengumuman'));
    }
}
