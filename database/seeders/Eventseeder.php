<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bidangs')->truncate();
        DB::table('sub_events')->truncate();
        DB::table('events')->truncate();

        $events = [
            ['id' => 1, 'nama_event' => 'LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)', 'jenis' => 'INOTEK'],
            ['id' => 2, 'nama_event' => 'INOVASI DAERAH KAB. MAGETAN',                'jenis' => 'INODA'],
            ['id' => 3, 'nama_event' => 'PAMERAN INOVASI DAN TEKNOLOGI',               'jenis' => 'INOTEK'],
            ['id' => 4, 'nama_event' => 'KOMPETISI INOVASI DIGITAL',                   'jenis' => 'INOTEK'],
        ];


        $subEvents = [
            ['id' => 1, 'event_id' => 1, 'tahun' => 2022, 'sub_event' => 'LOMBA INOTEK 2022',                                'kategori' => 'SEMUA BIDANG', 'mulai' => '2022-08-12', 'berakhir' => '2022-10-03'],
            ['id' => 2, 'event_id' => 1, 'tahun' => 2023, 'sub_event' => 'LOMBA INOTEK (INOTEK AWARD) 2023',                 'kategori' => 'SEMUA BIDANG', 'mulai' => '2023-03-15', 'berakhir' => '2023-07-23'],
            ['id' => 3, 'event_id' => 2, 'tahun' => 2023, 'sub_event' => 'PELAPORAN INOVASI DAERAH 2023',                    'kategori' => 'SEMUA BIDANG', 'mulai' => '2023-09-09', 'berakhir' => '2023-12-20'],
            ['id' => 4, 'event_id' => 1, 'tahun' => 2024, 'sub_event' => 'LOMBA INOVASI DAN TEKNOLOGI 2024',                 'kategori' => 'SEMUA',        'mulai' => '2024-04-01', 'berakhir' => '2025-03-31'],
            ['id' => 5, 'event_id' => 2, 'tahun' => 2024, 'sub_event' => 'PELAPORAN INOVASI DAERAH 2024 & INODA AWARD 2025', 'kategori' => 'SEMUA BIDANG', 'mulai' => '2024-01-01', 'berakhir' => '2025-12-31'],
            ['id' => 6, 'event_id' => 3, 'tahun' => 2025, 'sub_event' => 'PAMERAN INOTEK 2025',                              'kategori' => null,           'mulai' => '2025-01-01', 'berakhir' => '2025-12-31'],
            ['id' => 7, 'event_id' => 4, 'tahun' => 2025, 'sub_event' => 'KOMPETISI INOVASI DIGITAL 2025',                   'kategori' => null,           'mulai' => '2025-01-01', 'berakhir' => '2025-12-31'],
        ];


        $bidangs = [
            // Sub Event 1
            ['sub_event_id' => 1, 'nama' => 'Teknologi Informasi', 'status' => 'aktif'],
            ['sub_event_id' => 1, 'nama' => 'Pertanian',           'status' => 'tidak_aktif'],
            // Sub Event 2
            ['sub_event_id' => 2, 'nama' => 'Kesehatan',           'status' => 'aktif'],
            ['sub_event_id' => 2, 'nama' => 'Pendidikan',          'status' => 'aktif'],
            // Sub Event 3
            ['sub_event_id' => 3, 'nama' => 'Lingkungan Hidup',    'status' => 'aktif'],
            ['sub_event_id' => 3, 'nama' => 'Energi Terbarukan',   'status' => 'tidak_aktif'],
            // Sub Event 4
            ['sub_event_id' => 4, 'nama' => 'Teknologi Informasi', 'status' => 'aktif'],
            ['sub_event_id' => 4, 'nama' => 'Kesehatan',           'status' => 'aktif'],
            ['sub_event_id' => 4, 'nama' => 'Pertanian',           'status' => 'aktif'],
            ['sub_event_id' => 4, 'nama' => 'Pendidikan',          'status' => 'aktif'],
            ['sub_event_id' => 4, 'nama' => 'Lingkungan Hidup',    'status' => 'aktif'],
        ];

    }
}