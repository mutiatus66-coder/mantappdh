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
        // Kosongkan data lama jika ingin generate ulang
        // PenilaianUsulan::truncate();

        $usulans = Usulan::query()
            ->where('is_submitted', true)
            ->get();

        if ($usulans->isEmpty()) {
            $this->command->warn(
                'PenilaianUsulanSeeder: tidak ada usulan untuk dinilai. Jalankan UsulanSeeder dulu.'
            );
            return;
        }

        $this->command->info(
            "PenilaianUsulanSeeder: {$usulans->count()} usulan akan dinilai."
        );

        $total = 0;

        foreach ($usulans as $usulan) {

            $penilais = Penilai::query()
                ->where('sub_event_id', $usulan->sub_event_id)
                ->get();

            if ($penilais->isEmpty()) {
                $this->command->warn(
                    "Usulan #{$usulan->id}: belum ada penilai. Jalankan PenilaiSeeder."
                );
                continue;
            }

            $indikators = Indikator::query()
                ->where('sub_event_id', $usulan->sub_event_id)
                ->get();

            if ($indikators->isEmpty()) {
                $this->command->warn(
                    "Usulan #{$usulan->id}: belum ada indikator. Jalankan IndikatorSeeder."
                );
                continue;
            }

            foreach ($penilais as $penilai) {

                foreach ($indikators as $indikator) {

                    $keterangans = KeteranganIndikator::query()
                        ->where('indikator_id', $indikator->id)
                        ->get();

                    if ($keterangans->isEmpty()) {
                        continue;
                    }

                    $ket = $keterangans->random();

                    $nilai = rand(
                        (int) $ket->nilai_minimal,
                        (int) $ket->nilai_maksimal
                    );

                    PenilaianUsulan::updateOrCreate(
                        [
                            'usulan_id' => $usulan->id,
                            'penilai_id' => $penilai->id,
                            'keterangan_indikator_id' => $ket->id,
                        ],
                        [
                            'nilai' => $nilai,
                        ]
                    );

                    $total++;
                }
            }
        }

        $this->command->info(
            "PenilaianUsulanSeeder: {$total} baris nilai Tahap 1 berhasil dibuat."
        );
    }
}