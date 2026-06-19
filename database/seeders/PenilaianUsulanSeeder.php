<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usulan;
use App\Models\Penilai;
use App\Models\Indikator;
use App\Models\KeteranganIndikator;
use App\Models\PenilaianUsulan;

class PenilaianUsulanSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya usulan berstatus "Selesai" yang diberi nilai lengkap.
        // Usulan "Sedang Dinilai" sengaja DIBIARKAN tanpa nilai supaya
        // aturan UC-09 (reset status ke "Melengkapi Data") bisa diuji.
        
        $usulans = Usulan::query()->where('is_submitted', true)
            ->where('status', 'Selesai')
            ->get();

        if ($usulans->isEmpty()) {
            $this->command->warn('PenilaianUsulanSeeder: tidak ada usulan "Selesai". Jalankan UsulanSeeder dulu.');
            return;
        }

        $total = 0;

        foreach ($usulans as $usulan) {
            $penilai = Penilai::query()->where('sub_event_id', $usulan->sub_event_id)->get();
            if ($penilai->isEmpty()) {
                $this->command->warn("  - Usulan #{$usulan->id}: belum ada penilai di sub event. Jalankan PenilaiSeeder.");
                continue;
            }

            $indikators = Indikator::query()->where('sub_event_id', $usulan->sub_event_id)->get();
            if ($indikators->isEmpty()) {
                $this->command->warn("  - Usulan #{$usulan->id}: belum ada indikator tahap 1. Jalankan IndikatorSeeder.");
                continue;
            }

            foreach ($penilai as $penilai) {
                foreach ($indikators as $indikator) {
                    // Setiap penilai memilih 1 keterangan per indikator, lalu memberi nilai dalam rentangnya
                    $keterangans = KeteranganIndikator::query()->where('indikator_id', $indikator->id)->get();
                    if ($keterangans->isEmpty()) continue;

                    $ket   = $keterangans->random();
                    $nilai = rand((int) $ket->nilai_minimal, (int) $ket->nilai_maksimal);

                    PenilaianUsulan::firstOrCreate(
                        [
                            'usulan_id'               => $usulan->id,
                            'penilai_id'              => $penilai->id,
                            'keterangan_indikator_id' => $ket->id,
                        ],
                        ['nilai' => $nilai]
                    );
                    $total++;
                }
            }
        }

        $this->command->info("PenilaianUsulanSeeder: {$total} baris nilai Tahap 1 di-seed.");
    }
}