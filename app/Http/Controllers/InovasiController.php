<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InovasiController extends Controller
{
    public function riwayat()
    {
        $inovasi = [
            [
                'id' => 1,
                'status' => 'Melengkapi Data',
                'inovator' => 'DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL',
                'nama_inovasi' => 'SI-DICO',
                'nama_tim' => 'DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL',
                'ketua_nama' => 'DEWI SRI HANDAYANI, S.Sos',
                'ketua_email' => 'dewi.sri@magetan.go.id',
                'ketua_wa' => '08155618417',
            ],
            // Tambahkan data lain sesuai screenshot jika perlu
        ];

        return view('inovasi.riwayat', compact('inovasi'));
    }

    public function rekapNilai()
    {
        $rekap = [
            [
                'id' => 1,
                'inovasi' => 'SI-DICO',
                'instansi' => 'DINAS DUKCAPIL',
                'link_youtube' => 'https://youtube.com/watch?v=example',
                'no_hp' => '08123456789',
                'kategori' => 'Umum',
                'nilai_t1' => 85,
                'nilai_t2' => 90,
                'nilai_total' => 87.5,
            ],
            // Tambahkan data lain sesuai kebutuhan
        ];

        return view('inovasi.rekapnilai', compact('rekap'));
    }
}
