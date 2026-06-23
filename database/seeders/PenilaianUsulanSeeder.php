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
        // ── Strategi seeding ──────────────────────────────────────────────
        // 1. Semua usulan "Selesai"        → DINILAI penuh.
        // 2. Sebagian usulan "Sedang Dinilai" → DINILAI (agar rekap berisi).
        // 3. Sisa usulan "Sedang Dinilai"  → DIBIARKAN tanpa nilai
        //    supaya aturan UC-09 (reset status ke "Melengkapi Data") tetap bisa diuji.

        $selesai = Usulan::query()
            ->where('is_submitted', true)
            ->where('status', 'Selesai')
            ->get();

        // Ambil ~50% usulan "Sedang Dinilai" untuk ikut dinilai.
        $sedangDinilai = Usulan::query()
            ->where('is_submitted', true)
            ->where('status', 'Sedang Dinilai')
            ->get();

        $dinilaiSebagian = $sedangDinilai->shuffle()
            ->take((int) ceil($sedangDinilai->count() / 2));

        // Gabungkan usulan yang akan diberi nilai.
        $usulans = $selesai->merge($dinilaiSebagian);

        if ($usulans->isEmpty()) {
            $this->command->warn('PenilaianUsulanSeeder: tidak ada usulan untuk dinilai. Jalankan UsulanSeeder dulu.');
            return;
        }

        $this->command->info(sprintf(
            'PenilaianUsulanSeeder: %d "Selesai" + %d "Sedang Dinilai" (dari %d) akan dinilai. %d "Sedang Dinilai" dibiarkan kosong untuk uji UC-09.',
            $selesai->count(),
            $dinilaiSebagian->count(),
            $sedangDinilai->count(),
            $sedangDinilai->count() - $dinilaiSebagian->count()
        ));

        $total = 0;

        foreach ($usulans as $usulan) {
            $penilais = Penilai::query()->where('sub_event_id', $usulan->sub_event_id)->get();
            if ($penilais->isEmpty()) {
                $this->command->warn("  - Usulan #{$usulan->id}: belum ada penilai di sub event. Jalankan PenilaiSeeder.");
                continue;
            }

            $indikators = Indikator::query()->where('sub_event_id', $usulan->sub_event_id)->get();
            if ($indikators->isEmpty()) {
                $this->command->warn("  - Usulan #{$usulan->id}: belum ada indikator tahap 1. Jalankan IndikatorSeeder.");
                continue;
            }

            foreach ($penilais as $penilai) {
                foreach ($indikators as $indikator) {
                    // Setiap penilai memilih 1 keterangan per indikator, lalu memberi nilai dalam rentangnya.
                    $keterangans = KeteranganIndikator::query()
                        ->where('indikator_id', $indikator->id)
                        ->get();
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