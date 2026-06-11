<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubEvent;
use App\Models\Penilai;

class PenilaiSeeder extends Seeder
{
    public function run(): void
    {
        $penilaiData = [
            ['nama' => 'Dr. Ahmad Fauzi, M.T.',       'email' => 'ahmad.fauzi@example.com'],
            ['nama' => 'Prof. Siti Rahayu, Ph.D.',    'email' => 'siti.rahayu@example.com'],
            ['nama' => 'Ir. Budi Santoso, M.Sc.',     'email' => 'budi.santoso@example.com'],
            ['nama' => 'Dr. Dewi Kusuma, M.Si.',      'email' => 'dewi.kusuma@example.com'],
            ['nama' => 'Drs. Hendra Wijaya, M.M.',    'email' => 'hendra.wijaya@example.com'],
            ['nama' => 'Dr. Rina Fitriani, M.Pd.',    'email' => 'rina.fitriani@example.com'],
        ];

        // Ambil sub event terbaru (2025) untuk masing-masing jenis
        $subEvents = SubEvent::orderBy('tahun', 'desc')->take(2)->get();

        foreach ($subEvents as $index => $se) {
            // Assign 3 penilai per sub event
            $slice = array_slice($penilaiData, $index * 3, 3);
            foreach ($slice as $p) {
                Penilai::firstOrCreate(
                    ['email' => $p['email']],
                    [
                        'sub_event_id' => $se->id,
                        'nama'         => $p['nama'],
                        'email'        => $p['email'],
                        'user_id'      => null,
                    ]
                );
            }
        }
    }
}