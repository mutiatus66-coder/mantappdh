<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubEventSeeder extends Seeder
{
    public function run(): void
{
    $data = [
        ['event_id' => 1, 'tahun' => 2022, 'sub_event' => 'LOMBA INOTEK 2022', 'kategori' => 'SEMUA BIDANG', 'mulai' => '2022-08-12', 'berakhir' => '2022-10-03'],
        ['event_id' => 1, 'tahun' => 2023, 'sub_event' => 'LOMBA INOTEK (INOTEK AWARD) 2023', 'kategori' => 'SEMUA BIDANG', 'mulai' => '2023-03-15', 'berakhir' => '2023-07-23'],
        ['event_id' => 2, 'tahun' => 2023, 'sub_event' => 'PELAPORAN INOVASI DAERAH 2023', 'kategori' => 'SEMUA BIDANG', 'mulai' => '2023-09-09', 'berakhir' => '2023-12-20'],
        ['event_id' => 1, 'tahun' => 2024, 'sub_event' => 'LOMBA INOVASI DAN TEKNOLOGI 2024', 'kategori' => 'SEMUA', 'mulai' => '2024-04-01', 'berakhir' => '2025-03-31'],
    ];

    foreach ($data as $row) {
        \App\Models\SubEvent::create($row);
    }
}
}
