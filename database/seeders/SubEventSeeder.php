<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\SubEvent;

class SubEventSeeder extends Seeder
{
    public function run(): void
    {
        $inotek = Event::query()->where('jenis', 'INOTEK')->first();
        $inoda  = Event::query()->where('jenis', 'INODA')->first();

        $subEvents = [
            [
                'event_id'  => $inotek?->id,
                'tahun'     => 2024,
                'sub_event' => 'INOTEK 2024',
                'kategori'  => 'SEMUA BIDANG',
                'mulai'     => '2024-01-15',
                'berakhir'  => '2024-06-30',
            ],
            [
                'event_id'  => $inotek?->id,
                'tahun'     => 2025,
                'sub_event' => 'INOTEK 2025',
                'kategori'  => 'SEMUA BIDANG',
                'mulai'     => '2025-02-01',
                'berakhir'  => '2025-07-31',
            ],
            [
                'event_id'  => $inoda?->id,
                'tahun'     => 2024,
                'sub_event' => 'INODA 2024',
                'kategori'  => 'SEMUA BIDANG',
                'mulai'     => '2024-03-01',
                'berakhir'  => '2024-08-31',
            ],
            [
                'event_id'  => $inoda?->id,
                'tahun'     => 2025,
                'sub_event' => 'INODA 2025',
                'kategori'  => 'SEMUA BIDANG',
                'mulai'     => '2025-03-01',
                'berakhir'  => '2025-09-30',
            ],
        ];

        foreach ($subEvents as $se) {
            if ($se['event_id']) {
                SubEvent::firstOrCreate(
                    ['event_id' => $se['event_id'], 'tahun' => $se['tahun'], 'sub_event' => $se['sub_event']],
                    $se
                );
            }
        }
    }
}