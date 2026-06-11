<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubEvent;
use App\Models\Inovator;

class InovatorSeeder extends Seeder
{
    public function run(): void
    {
        $subEvents = SubEvent::orderBy('tahun', 'desc')->get();

        $inovatorTemplate = [
            // Kategori umum
            [
                'inovator'     => 'CV. Teknologi Maju',
                'nama_inovasi' => 'Sistem Irigasi Otomatis Berbasis IoT',
                'kategori'     => 'umum',
            ],
            [
                'inovator'     => 'PT. Solusi Digital Nusantara',
                'nama_inovasi' => 'Aplikasi Pemantauan Kualitas Udara Real-Time',
                'kategori'     => 'umum',
            ],
            [
                'inovator'     => 'UD. Energi Mandiri',
                'nama_inovasi' => 'Panel Surya Portabel untuk UMKM',
                'kategori'     => 'umum',
            ],
            [
                'inovator'     => 'Kelompok Tani Makmur Sejahtera',
                'nama_inovasi' => 'Pupuk Organik Cair dari Limbah Ternak',
                'kategori'     => 'umum',
            ],
            [
                'inovator'     => 'Komunitas Kesehatan Masyarakat',
                'nama_inovasi' => 'Alat Deteksi Dini Stunting Portabel',
                'kategori'     => 'umum',
            ],
            // Kategori pelajar
            [
                'inovator'     => 'SMAN 1 Magetan',
                'nama_inovasi' => 'Tas Sekolah Penghasil Energi Kinetik',
                'kategori'     => 'pelajar',
            ],
            [
                'inovator'     => 'SMK Teknik Nusantara',
                'nama_inovasi' => 'Robot Pemilah Sampah Otomatis',
                'kategori'     => 'pelajar',
            ],
            [
                'inovator'     => 'SMA Negeri 2 Magetan',
                'nama_inovasi' => 'Aplikasi Belajar Bahasa Daerah Berbasis AR',
                'kategori'     => 'pelajar',
            ],
            [
                'inovator'     => 'SMK Kesehatan Bhakti Husada',
                'nama_inovasi' => 'Alat Pengukur Tekanan Darah Murah dan Akurat',
                'kategori'     => 'pelajar',
            ],
        ];

        foreach ($subEvents as $se) {
            foreach ($inovatorTemplate as $data) {
                Inovator::firstOrCreate(
                    [
                        'sub_event_id' => $se->id,
                        'inovator'     => $data['inovator'],
                        'nama_inovasi' => $data['nama_inovasi'],
                    ],
                    [
                        'sub_event_id' => $se->id,
                        'inovator'     => $data['inovator'],
                        'nama_inovasi' => $data['nama_inovasi'],
                        'kategori'     => $data['kategori'],
                    ]
                );
            }
        }
    }
}