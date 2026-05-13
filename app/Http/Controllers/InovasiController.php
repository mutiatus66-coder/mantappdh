<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin;

class InovasiController extends Controller
{
    public function riwayat()
{
    $subEvents = [
        ['id' => 1, 'nama' => 'LOMBA INOTEK 2022', 'total_usulan' => 10, 'dinilai' => 9],
        ['id' => 2, 'nama' => 'LOMBA INOTEK (INOTEK AWARD) 2023', 'total_usulan' => 7, 'dinilai' => 7],
        ['id' => 3, 'nama' => 'PELAPORAN INOVASI DAERAH 2023', 'total_usulan' => 3, 'dinilai' => 3],
        ['id' => 4, 'nama' => 'PELAPORAN INOVASIAWARDI 2023', 'total_usulan' => 5, 'dinilai' => 4],
        ['id' => 5, 'nama' => 'PELAPORAN INOVASI DAERAH 2024 & INODA AWARD 2025', 'total_usulan' => 10, 'dinilai' => 8],
        ['id' => 6, 'nama' => 'KOMPETISI INOVASI DAN TEKNOLOGI (INOTEK AWARD) 2025', 'total_usulan' => 9, 'dinilai' => 5],
        ['id' => 7, 'nama' => 'PELAPORAN INOVASI DAERAH 2026', 'total_usulan' => 6, 'dinilai' => 2],
        ['id' => 8, 'nama' => 'PAMERAN INOTEK 2025', 'total_usulan' => 4, 'dinilai' => 0],
    ];

    return view('inovasi.riwayat', compact('subEvents'));
}

public function rekapNilai()
{
    // Data bisa sama atau berbeda (misal total_usulan = total inovasi, dinilai = yang sudah punya nilai)
    $subEvents = [
        ['id' => 1, 'nama' => 'LOMBA INOTEK 2022', 'total_usulan' => 10, 'dinilai' => 9],
        ['id' => 2, 'nama' => 'LOMBA INOTEK (INOTEK AWARD) 2023', 'total_usulan' => 7, 'dinilai' => 7],
        ['id' => 3, 'nama' => 'PELAPORAN INOVASI DAERAH 2023', 'total_usulan' => 3, 'dinilai' => 3],
        ['id' => 4, 'nama' => 'PELAPORAN INOVASIAWARDI 2023', 'total_usulan' => 5, 'dinilai' => 4],
        ['id' => 5, 'nama' => 'PELAPORAN INOVASI DAERAH 2024 & INODA AWARD 2025', 'total_usulan' => 10, 'dinilai' => 8],
        ['id' => 6, 'nama' => 'KOMPETISI INOVASI DAN TEKNOLOGI (INOTEK AWARD) 2025', 'total_usulan' => 9, 'dinilai' => 5],
        ['id' => 7, 'nama' => 'PELAPORAN INOVASI DAERAH 2026', 'total_usulan' => 6, 'dinilai' => 2],
        ['id' => 8, 'nama' => 'PAMERAN INOTEK 2025', 'total_usulan' => 4, 'dinilai' => 0],
    ];

    return view('inovasi.rekapnilai', compact('subEvents'));
}

    public function usulan($subEventId)
    {
        $subEvents = [
            1 => 'LOMBA INOTEK 2022',
            2 => 'LOMBA INOTEK (INOTEK AWARD) 2023',
            3 => 'PELAPORAN INOVASI DAERAH 2024 & INODA AWARD 2025',
            4 => 'KOMPETISI INOVASI DAN TEKNOLOGI (INOTEK AWARD) 2025',
        ];
        $subEventNama = $subEvents[$subEventId] ?? 'Sub Event';

        $usulan = [
            [
                'judul' => 'SI-DICO',
                'instansi' => 'DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL',
                'link_youtube' => 'https://youtu.be/abc123',
                'no_hp' => '08155618417',
                'kategori' => 'Umum',
                'nilai_t1' => 85,
                'nilai_t2' => 90,
                'nilai_total' => 87.5,
            ],
            [
                'judul' => 'Aplikasi SISTAN',
                'instansi' => 'DINAS PERTANIAN',
                'link_youtube' => 'https://youtu.be/def456',
                'no_hp' => '08234567890',
                'kategori' => 'Pelajar',
                'nilai_t1' => 78,
                'nilai_t2' => 82,
                'nilai_total' => 80.0,
            ],
        ];

        return view('inovasi.usulan', compact('usulan', 'subEventNama', 'subEventId'));
    }

    public function usulanRiwayat($subEventId)
    {
        $subEventNama = $this->getSubEventName($subEventId); // atau array manual
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
        ];
        return view('inovasi.usulan_riwayat', compact('usulan', 'subEventNama'));
    }

    public function usulanNilai($subEventId)
    {
        $subEventNama = $this->getSubEventName($subEventId);

        $usulan = [
            [
                'judul' => 'SI-DICO',
                'instansi' => 'DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL',
                'link_youtube' => 'https://youtu.be/abc123',
                'no_hp' => '08155618417',
                'kategori' => 'Umum',
                'nilai_t1' => 85,
                'nilai_t2' => 90,
                'nilai_total' => 87.5,
            ],
            [
                'judul' => 'Aplikasi SISTAN',
                'instansi' => 'DINAS PERTANIAN',
                'link_youtube' => 'https://youtu.be/def456',
                'no_hp' => '08234567890',
                'kategori' => 'Pelajar',
                'nilai_t1' => 78,
                'nilai_t2' => 82,
                'nilai_total' => 80.0,
            ],
        ];

        return view('inovasi.usulan_nilai', compact('usulan', 'subEventNama'));
    }

    private function getSubEventName($id)
    {
        $subEvents = [
            1 => 'LOMBA INOTEK 2022',
            2 => 'LOMBA INOTEK (INOTEK AWARD) 2023',
            3 => 'PELAPORAN INOVASI DAERAH 2023',
            4 => 'PELAPORAN INOVASIAWARDI 2023',
            5 => 'PELAPORAN INOVASI DAERAH 2024 & INODA AWARD 2025',
            6 => 'KOMPETISI INOVASI DAN TEKNOLOGI (INOTEK AWARD) 2025',
            7 => 'PELAPORAN INOVASI DAERAH 2026',
        ];
        return $subEvents[$id] ?? 'Sub Event';
    }
}
