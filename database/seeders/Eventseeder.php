<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            ['nama_event' => 'Inovasi Teknologi Jawa Timur',    'jenis' => 'INOTEK'],
            ['nama_event' => 'Inovasi Daerah Jawa Timur',       'jenis' => 'INODA'],
            ['nama_event' => 'Inovasi Teknologi Nasional',      'jenis' => 'INOTEK'],
            ['nama_event' => 'Inovasi Daerah Kabupaten Magetan','jenis' => 'INODA'],
        ];

        foreach ($events as $event) {
            Event::firstOrCreate(
                ['nama_event' => $event['nama_event'], 'jenis' => $event['jenis']],
                $event
            );
        }
    }
}