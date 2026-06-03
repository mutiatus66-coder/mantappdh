<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('events')->count() > 0) {
            $this->command->info('Tabel events sudah ada datanya, dilewati.');
            return;
        }

        DB::table('events')->insert([
            ['nama_event' => 'LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)', 'jenis' => 'INOTEK', 'created_at' => now(), 'updated_at' => now()],
            ['nama_event' => 'INOVASI DAERAH KAB. MAGETAN',                'jenis' => 'INODA',  'created_at' => now(), 'updated_at' => now()],
            ['nama_event' => 'PAMERAN INOVASI DAN TEKNOLOGI',               'jenis' => 'INOTEK', 'created_at' => now(), 'updated_at' => now()],
            ['nama_event' => 'KOMPETISI INOVASI DIGITAL',                   'jenis' => 'INOTEK', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->command->info('4 event berhasil dimasukkan.');
    }
}