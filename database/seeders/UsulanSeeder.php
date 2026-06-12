<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubEvent;
use App\Models\Usulan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsulanSeeder extends Seeder
{
    public function run(): void
    {
        $subEvent = SubEvent::orderBy('tahun', 'desc')->first();

        if (!$subEvent) {
            return;
        }

        // Buat dummy user jika belum ada
        $user = User::firstOrCreate(
            ['email' => 'peserta.dummy@example.com'],
            [
                'nama'     => 'Peserta Dummy',
                'email'    => 'peserta.dummy@example.com',
                'password' => Hash::make('password'),
            ]
        );

        $usulans = [
            [
                'user_id'      => $user->id,
                'sub_event_id' => $subEvent->id,
                'judul'        => 'Sistem Pemantauan Tanah Pertanian Berbasis Sensor',
                'inovator'     => 'Kelompok Tani Maju Bersama',
                'nama_inovasi' => 'AgroSense',
                'nama_tim'     => 'Tim AgroSense',
                'ketua_nama'   => 'Budi Prasetyo',
                'ketua_email'  => 'budi.prasetyo@email.com',
                'ketua_wa'     => '081234567890',
                'status'       => 'Melengkapi Data',
                'is_submitted' => false,
            ],
            [
                'user_id'      => $user->id,
                'sub_event_id' => $subEvent->id,
                'judul'        => 'Aplikasi Telemedicine untuk Daerah Terpencil',
                'inovator'     => 'Puskesmas Kecamatan Barat',
                'nama_inovasi' => 'TeleMed Rural',
                'nama_tim'     => 'Tim Kesehatan Digital',
                'ketua_nama'   => 'dr. Sari Indah',
                'ketua_email'  => 'sari.indah@email.com',
                'ketua_wa'     => '082345678901',
                'status'       => 'Melengkapi Data',
                'is_submitted' => false,
            ],
            [
                'user_id'      => $user->id,
                'sub_event_id' => $subEvent->id,
                'judul'        => 'Pengolahan Sampah Plastik Menjadi Bahan Bakar',
                'inovator'     => 'SMKN 1 Magetan',
                'nama_inovasi' => 'EcoFuel',
                'nama_tim'     => 'Tim Green Energy SMKN 1',
                'ketua_nama'   => 'Ahmad Rizki',
                'ketua_email'  => 'ahmad.rizki@email.com',
                'ketua_wa'     => '083456789012',
                'status'       => 'Sedang Dinilai',
                'is_submitted' => true,
            ],
            [
                'user_id'      => $user->id,
                'sub_event_id' => $subEvent->id,
                'judul'        => 'Alat Pengering Hasil Panen Tenaga Surya',
                'inovator'     => 'UD. Surya Tani',
                'nama_inovasi' => 'SolDry',
                'nama_tim'     => 'Tim SolDry',
                'ketua_nama'   => 'Wahyu Setiawan',
                'ketua_email'  => 'wahyu.setiawan@email.com',
                'ketua_wa'     => '084567890123',
                'status'       => 'Sedang Dinilai',
                'is_submitted' => true,
            ],
            [
                'user_id'      => $user->id,
                'sub_event_id' => $subEvent->id,
                'judul'        => 'Media Pembelajaran Interaktif Berbasis Gamifikasi',
                'inovator'     => 'SMA Negeri 3 Magetan',
                'nama_inovasi' => 'EduPlay',
                'nama_tim'     => 'Tim EduPlay SMA 3',
                'ketua_nama'   => 'Citra Dewi',
                'ketua_email'  => 'citra.dewi@email.com',
                'ketua_wa'     => '085678901234',
                'status'       => 'Selesai',
                'is_submitted' => true,
            ],
        ];

        foreach ($usulans as $item) {
            Usulan::firstOrCreate(
                [
                    'sub_event_id' => $item['sub_event_id'],
                    'nama_inovasi' => $item['nama_inovasi'],
                ],
                $item
            );
        }
    }
}