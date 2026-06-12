<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubEvent;
use App\Models\Bidang;

class BidangSeeder extends Seeder
{
    public function run(): void
    {
        $namaInotek = [
            'Kesehatan',
            'Pendidikan',
            'Pertanian & Pangan',
            'Lingkungan Hidup',
            'Energi Terbarukan',
            'Teknologi Informasi',
            'Industri & Manufaktur',
            'Sosial & Kemanusiaan',
        ];

        $namaInoda = [
            'Tata Kelola Pemerintahan',
            'Pelayanan Publik',
            'Pemberdayaan Masyarakat',
            'Perekonomian Daerah',
            'Infrastruktur',
            'Pariwisata & Budaya',
        ];

        $subEvents = SubEvent::with('event')->get();

        foreach ($subEvents as $se) {
            $jenis = $se->event->jenis ?? null;
            $daftar = $jenis === 'INOTEK' ? $namaInotek : $namaInoda;

            foreach ($daftar as $nama) {
                Bidang::firstOrCreate(
                    ['sub_event_id' => $se->id, 'nama' => $nama],
                    ['sub_event_id' => $se->id, 'nama' => $nama, 'status' => 'aktif']
                );
            }
        }
    }
}