<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubEventSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Insert data events dulu (foreign key) ──────────────────────
        $eventMap = [
            'LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)',
            'INOVASI DAERAH KAB. MAGETAN',
            'PAMERAN INOVASI DAN TEKNOLOGI',
            'KOMPETISI INOVASI DIGITAL',
        ];

        foreach ($eventMap as $namaEvent) {
            DB::table('events')->insertOrIgnore([
                'nama_event' => $namaEvent,
                'jenis'      => 'LOMBA',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Ambil id events yang baru dibuat
        $events = DB::table('events')->pluck('id', 'nama_event');

        // ── 2. Insert sub_events ──────────────────────────────────────────
        if (DB::table('sub_events')->count() > 0) {
            $this->command->info('Tabel sub_events sudah ada datanya, seeder dilewati.');
            return;
        }

        $data = [
            [
                'event_id'   => $events['LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)'],
                'tahun'      => 2022,
                'sub_event'  => 'LOMBA INOTEK 2022',
                'kategori'   => 'SEMUA BIDANG',
                'mulai'      => '2022-08-12',
                'berakhir'   => '2022-10-03',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_id'   => $events['LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)'],
                'tahun'      => 2023,
                'sub_event'  => 'LOMBA INOTEK (INOTEK AWARD) 2023',
                'kategori'   => 'SEMUA BIDANG',
                'mulai'      => '2023-03-15',
                'berakhir'   => '2023-07-23',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_id'   => $events['INOVASI DAERAH KAB. MAGETAN'],
                'tahun'      => 2023,
                'sub_event'  => 'PELAPORAN INOVASI DAERAH 2023',
                'kategori'   => 'SEMUA BIDANG',
                'mulai'      => '2023-09-09',
                'berakhir'   => '2023-12-20',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_id'   => $events['LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)'],
                'tahun'      => 2024,
                'sub_event'  => 'LOMBA INOVASI DAN TEKNOLOGI 2024',
                'kategori'   => 'SEMUA',
                'mulai'      => '2024-04-01',
                'berakhir'   => '2025-03-31',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_id'   => $events['INOVASI DAERAH KAB. MAGETAN'],
                'tahun'      => 2024,
                'sub_event'  => 'PELAPORAN INOVASI DAERAH 2024 & INODA AWARD 2025',
                'kategori'   => 'SEMUA BIDANG',
                'mulai'      => '2024-09-01',
                'berakhir'   => '2025-02-28',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_id'   => $events['PAMERAN INOVASI DAN TEKNOLOGI'],
                'tahun'      => 2025,
                'sub_event'  => 'PAMERAN INOTEK 2025',
                'kategori'   => 'SEMUA BIDANG',
                'mulai'      => '2025-01-10',
                'berakhir'   => '2025-06-30',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_id'   => $events['KOMPETISI INOVASI DIGITAL'],
                'tahun'      => 2025,
                'sub_event'  => 'KOMPETISI INOVASI DIGITAL 2025',
                'kategori'   => 'TEKNOLOGI',
                'mulai'      => '2025-03-01',
                'berakhir'   => '2025-08-31',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('sub_events')->insert($data);

        $this->command->info('Seeder selesai: ' . count($data) . ' sub event berhasil dimasukkan.');
    }
}