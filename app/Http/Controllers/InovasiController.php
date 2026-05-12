<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin;

class InovasiController extends Controller
{
    // Halaman riwayat (card grid sesuai screenshot)
    public function riwayat()
    {
        $subEvents = [
            ['id' => 1, 'nama' => 'LOMBA INOTEK 2022'],
            ['id' => 2, 'nama' => 'LOMBA INOTEK (INOTEK AWARD) 2023'],
            ['id' => 3, 'nama' => 'PELAPORAN INOVASI DAERAH 2024 & INODA AWARD 2025'],
            ['id' => 4, 'nama' => 'KOMPETISI INOVASI DAN TEKNOLOGI (INOTEK AWARD) 2025'],
            ['id' => 5, 'nama' => 'PELAPORAN INOVASI DAERAH 2026'],
            ['id' => 6, 'nama' => 'PELAPORAN INOVASI DAERAH 2023'],
            ['id' => 7, 'nama' => 'PAMERAN INOTEK'],
            ['id' => 8, 'nama' => 'PELAPORAN INOVASI TEKNOLOGI (INOTEK AWARD) 2025'],
            ['id' => 9, 'nama' => 'PALEMBANG INOVASI DAERAH 2023'],
            ['id' => 10, 'nama' => 'PALEMBANG INOVASI DAERAH (INOTEK AWARD) 2025'],
        ];
        return view('inovasi.riwayat', compact('subEvents'));
    }

    // Halaman rekap nilai (tabel dengan filter kategori)
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
            // tambah dummy lain jika perlu
        ];
        return view('inovasi.rekapnilai', compact('rekap'));
    }


    // Sementara untuk detail usulan (bisa diisi nanti)
public function usulan($subEventId)
{
    // Data nama sub event (sesuai dengan id yang diklik dari halaman riwayat)
    $subEvents = [
        1 => 'LOMBA INOTEK 2022',
        2 => 'LOMBA INOTEK (INOTEK AWARD) 2023',
        3 => 'PELAPORAN INOVASI DAERAH 2024 & INODA AWARD 2025',
        4 => 'KOMPETISI INOVASI DAN TEKNOLOGI (INOTEK AWARD) 2025',
        5 => 'PELAPORAN INOVASI DAERAH 2026',
        6 => 'PELAPORAN INOVASI DAERAH 2023',
        7 => 'PAMERAN INOTEK',
        8 => 'PELAPORAN INOVASI TEKNOLOGI (INOTEK AWARD) 2025',
        9 => 'PALEMBANG INOVASI DAERAH 2023',
        10 => 'PALEMBANG INOVASI DAERAH (INOTEK AWARD) 2025',
    ];
    $subEventNama = $subEvents[$subEventId] ?? 'Sub Event tidak dikenal';

    // Data dummy usulan (biar tampil seperti screenshot)
    $usulan = [
        [
            'status' => 'Melengkapi Data',
            'inovator' => 'DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL',
            'nama_inovasi' => 'SI-DICO',
            'nama_tim' => 'DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL',
            'ketua_nama' => 'DEWI SRI HANDAYANI, S.Sos',
            'ketua_email' => 'dewi.sri@magetan.go.id',
            'ketua_wa' => '08155618417',
        ],
        // Bisa tambah data lain jika diperlukan
    ];

    return view('inovasi.usulan', compact('usulan', 'subEventNama', 'subEventId'));
}

}
