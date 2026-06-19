<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usulan;
use App\Models\Penilai;
use App\Models\IndikatorTahap2;
use App\Models\KeteranganTahap2;
use App\Models\Pemenang;

class PemenangSeeder extends Seeder
{
    public function run(): void
    {
        // Tahap 2 hanya untuk usulan yang sudah "Selesai"
        $usulans = Usulan::query()->where('is_submitted', true)
            ->where('status', 'Selesai')
            ->get();

        if ($usulans->isEmpty()) {
            $this->command->warn('PemenangSeeder: tidak ada usulan "Selesai".');
            return;
        }

        $total = 0;

        foreach ($usulans as $usulan) {
            $penilais = Penilai::query()->where('sub_event_id', $usulan->sub_event_id)->get();
            if ($penilais->isEmpty()) continue;

            $indikators = IndikatorTahap2::query()->where('sub_event_id', $usulan->sub_event_id)->get();
            if ($indikators->isEmpty()) {
                $this->command->warn("  - Usulan #{$usulan->id}: belum ada indikator tahap 2.");
                continue;
            }

            foreach ($penilais as $penilai) {
                foreach ($indikators as $indikator) {
                    $keterangans = KeteranganTahap2::query()->where('indikator_tahap2_id', $indikator->id)->get();
                    if ($keterangans->isEmpty()) continue;

                    $ket   = $keterangans->random();
                    $nilai = rand((int) $ket->nilai_minimal, (int) $ket->nilai_maksimal);

                    // ⚠️ VERIFIKASI nama kolom ini terhadap migration `pemenang` kamu
                    Pemenang::firstOrCreate(
                        [
                            'usulan_id'            => $usulan->id,
                            'penilai_id'           => $penilai->id,
                            'keterangan_tahap2_id' => $ket->id,
                        ],
                        ['nilai' => $nilai]
                    );
                    $total++;
                }
            }
        }

        $this->command->info("PemenangSeeder: {$total} baris nilai Tahap 2 di-seed.");
    }
}