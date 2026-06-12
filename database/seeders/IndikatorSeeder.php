<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubEvent;
use App\Models\Indikator;
use App\Models\KeteranganIndikator;
use App\Models\FormulasiTahap1;
use App\Models\IndikatorTahap2;
use App\Models\KeteranganTahap2;
use App\Models\FormulasiTahap2;

class IndikatorSeeder extends Seeder
{
    public function run(): void
    {
        $subEvents = SubEvent::all();

        foreach ($subEvents as $se) {
            $this->seedTahap1($se->id);
            $this->seedTahap2($se->id);
        }
    }

    // ══════════════════════════════════════════════════════════
    // TAHAP 1
    // ══════════════════════════════════════════════════════════
    private function seedTahap1(int $subEventId): void
    {
        FormulasiTahap1::firstOrCreate(
            ['sub_event_id' => $subEventId],
            [
                'sub_event_id'    => $subEventId,
                'nilai_makalah'   => 40,
                'nilai_substansi' => 60,
            ]
        );

        $indikators = [
            [
                'nama_indikator' => 'Orisinalitas Inovasi',
                'keterangans'    => [
                    ['keterangan' => 'Inovasi sepenuhnya baru dan belum pernah ada sebelumnya',        'nilai_minimal' => 85, 'nilai_maksimal' => 100],
                    ['keterangan' => 'Inovasi merupakan pengembangan signifikan dari yang sudah ada',   'nilai_minimal' => 70, 'nilai_maksimal' => 84],
                    ['keterangan' => 'Inovasi merupakan pengembangan minor dari yang sudah ada',        'nilai_minimal' => 55, 'nilai_maksimal' => 69],
                    ['keterangan' => 'Inovasi merupakan adopsi/replikasi dengan sedikit modifikasi',    'nilai_minimal' => 40, 'nilai_maksimal' => 54],
                    ['keterangan' => 'Belum menunjukkan unsur kebaruan yang jelas',                    'nilai_minimal' => 0,  'nilai_maksimal' => 39],
                ],
            ],
            [
                'nama_indikator' => 'Manfaat dan Dampak',
                'keterangans'    => [
                    ['keterangan' => 'Memberikan dampak luas dan terukur bagi masyarakat/lingkungan',   'nilai_minimal' => 85, 'nilai_maksimal' => 100],
                    ['keterangan' => 'Memberikan manfaat nyata namun terbatas pada kelompok tertentu', 'nilai_minimal' => 70, 'nilai_maksimal' => 84],
                    ['keterangan' => 'Manfaat ada namun belum sepenuhnya terbukti',                    'nilai_minimal' => 55, 'nilai_maksimal' => 69],
                    ['keterangan' => 'Manfaat masih bersifat potensi, belum diimplementasikan',        'nilai_minimal' => 40, 'nilai_maksimal' => 54],
                    ['keterangan' => 'Manfaat tidak jelas atau tidak relevan',                         'nilai_minimal' => 0,  'nilai_maksimal' => 39],
                ],
            ],
            [
                'nama_indikator' => 'Kelayakan Teknis',
                'keterangans'    => [
                    ['keterangan' => 'Sudah diuji, terbukti berfungsi, dan siap diimplementasikan',    'nilai_minimal' => 85, 'nilai_maksimal' => 100],
                    ['keterangan' => 'Sudah diuji dengan hasil positif namun perlu penyempurnaan',     'nilai_minimal' => 70, 'nilai_maksimal' => 84],
                    ['keterangan' => 'Masih dalam tahap pengujian',                                    'nilai_minimal' => 55, 'nilai_maksimal' => 69],
                    ['keterangan' => 'Baru pada tahap prototipe awal',                                 'nilai_minimal' => 40, 'nilai_maksimal' => 54],
                    ['keterangan' => 'Masih konsep, belum ada bukti kelayakan teknis',                 'nilai_minimal' => 0,  'nilai_maksimal' => 39],
                ],
            ],
            [
                'nama_indikator' => 'Keberlanjutan',
                'keterangans'    => [
                    ['keterangan' => 'Ada rencana keberlanjutan yang jelas dan dukungan pendanaan',     'nilai_minimal' => 85, 'nilai_maksimal' => 100],
                    ['keterangan' => 'Ada rencana keberlanjutan namun dukungan belum pasti',            'nilai_minimal' => 70, 'nilai_maksimal' => 84],
                    ['keterangan' => 'Ada gambaran keberlanjutan namun belum terperinci',               'nilai_minimal' => 55, 'nilai_maksimal' => 69],
                    ['keterangan' => 'Keberlanjutan masih dipertanyakan',                              'nilai_minimal' => 40, 'nilai_maksimal' => 54],
                    ['keterangan' => 'Tidak ada rencana keberlanjutan',                                'nilai_minimal' => 0,  'nilai_maksimal' => 39],
                ],
            ],
        ];

        foreach ($indikators as $ind) {
            $indikator = Indikator::firstOrCreate(
                ['sub_event_id' => $subEventId, 'nama_indikator' => $ind['nama_indikator']],
                ['sub_event_id' => $subEventId, 'nama_indikator' => $ind['nama_indikator']]
            );

            foreach ($ind['keterangans'] as $ket) {
                KeteranganIndikator::firstOrCreate(
                    ['indikator_id' => $indikator->id, 'keterangan' => $ket['keterangan']],
                    array_merge(['indikator_id' => $indikator->id], $ket)
                );
            }
        }
    }

    // ══════════════════════════════════════════════════════════
    // TAHAP 2
    // ══════════════════════════════════════════════════════════
    private function seedTahap2(int $subEventId): void
    {
        FormulasiTahap2::firstOrCreate(
            ['sub_event_id' => $subEventId],
            [
                'sub_event_id'   => $subEventId,
                'nilai_inovasi'  => 60,
                'nilai_peragaan' => 40,
            ]
        );

        $indikators = [
            // ── Substansi Inovasi ───────────────────────────────────────
            [
                'nama_indikator' => 'Kedalaman Analisis Masalah',
                'jenis'          => 'Subtansi Inovasi',
                'keterangans'    => [
                    ['keterangan' => 'Analisis masalah sangat mendalam didukung data empiris',         'nilai_minimal' => 85, 'nilai_maksimal' => 100],
                    ['keterangan' => 'Analisis memadai dengan data pendukung yang cukup',               'nilai_minimal' => 70, 'nilai_maksimal' => 84],
                    ['keterangan' => 'Analisis cukup namun data pendukung terbatas',                   'nilai_minimal' => 55, 'nilai_maksimal' => 69],
                    ['keterangan' => 'Analisis dangkal, masalah tidak dijelaskan dengan baik',          'nilai_minimal' => 0,  'nilai_maksimal' => 54],
                ],
            ],
            [
                'nama_indikator' => 'Inovasi dan Kreativitas Solusi',
                'jenis'          => 'Subtansi Inovasi',
                'keterangans'    => [
                    ['keterangan' => 'Solusi sangat kreatif, belum ada solusi serupa sebelumnya',       'nilai_minimal' => 85, 'nilai_maksimal' => 100],
                    ['keterangan' => 'Solusi kreatif dengan modifikasi signifikan dari yang sudah ada', 'nilai_minimal' => 70, 'nilai_maksimal' => 84],
                    ['keterangan' => 'Solusi cukup kreatif namun tidak terlalu berbeda',               'nilai_minimal' => 55, 'nilai_maksimal' => 69],
                    ['keterangan' => 'Solusi konvensional, kurang unsur inovasi',                       'nilai_minimal' => 0,  'nilai_maksimal' => 54],
                ],
            ],
            [
                'nama_indikator' => 'Skalabilitas dan Replikasi',
                'jenis'          => 'Subtansi Inovasi',
                'keterangans'    => [
                    ['keterangan' => 'Dapat diterapkan secara luas dan mudah direplikasi daerah lain',  'nilai_minimal' => 85, 'nilai_maksimal' => 100],
                    ['keterangan' => 'Berpotensi direplikasi dengan penyesuaian tertentu',              'nilai_minimal' => 70, 'nilai_maksimal' => 84],
                    ['keterangan' => 'Replikasi terbatas pada kondisi yang sangat spesifik',            'nilai_minimal' => 55, 'nilai_maksimal' => 69],
                    ['keterangan' => 'Sulit direplikasi di luar konteks aslinya',                       'nilai_minimal' => 0,  'nilai_maksimal' => 54],
                ],
            ],
            // ── Peragaan ───────────────────────────────────────────────
            [
                'nama_indikator' => 'Kemampuan Presentasi',
                'jenis'          => 'Peragaan',
                'keterangans'    => [
                    ['keterangan' => 'Presentasi sangat jelas, sistematis, dan menarik',               'nilai_minimal' => 85, 'nilai_maksimal' => 100],
                    ['keterangan' => 'Presentasi jelas dan terstruktur',                               'nilai_minimal' => 70, 'nilai_maksimal' => 84],
                    ['keterangan' => 'Presentasi cukup baik namun kurang terstruktur',                 'nilai_minimal' => 55, 'nilai_maksimal' => 69],
                    ['keterangan' => 'Presentasi kurang jelas dan sulit dipahami',                     'nilai_minimal' => 0,  'nilai_maksimal' => 54],
                ],
            ],
            [
                'nama_indikator' => 'Demonstrasi Produk/Inovasi',
                'jenis'          => 'Peragaan',
                'keterangans'    => [
                    ['keterangan' => 'Demonstrasi berjalan sempurna dan meyakinkan',                   'nilai_minimal' => 85, 'nilai_maksimal' => 100],
                    ['keterangan' => 'Demonstrasi berjalan dengan baik, ada kendala kecil',            'nilai_minimal' => 70, 'nilai_maksimal' => 84],
                    ['keterangan' => 'Demonstrasi sebagian berhasil',                                  'nilai_minimal' => 55, 'nilai_maksimal' => 69],
                    ['keterangan' => 'Demonstrasi tidak berhasil atau tidak dilakukan',                'nilai_minimal' => 0,  'nilai_maksimal' => 54],
                ],
            ],
            [
                'nama_indikator' => 'Kemampuan Menjawab Pertanyaan',
                'jenis'          => 'Peragaan',
                'keterangans'    => [
                    ['keterangan' => 'Menjawab semua pertanyaan dengan tepat dan percaya diri',        'nilai_minimal' => 85, 'nilai_maksimal' => 100],
                    ['keterangan' => 'Menjawab sebagian besar pertanyaan dengan baik',                 'nilai_minimal' => 70, 'nilai_maksimal' => 84],
                    ['keterangan' => 'Menjawab beberapa pertanyaan, sebagian kurang tepat',            'nilai_minimal' => 55, 'nilai_maksimal' => 69],
                    ['keterangan' => 'Kesulitan menjawab pertanyaan dari juri',                        'nilai_minimal' => 0,  'nilai_maksimal' => 54],
                ],
            ],
        ];

        foreach ($indikators as $ind) {
            $indikator = IndikatorTahap2::firstOrCreate(
                [
                    'sub_event_id'   => $subEventId,
                    'nama_indikator' => $ind['nama_indikator'],
                    'jenis'          => $ind['jenis'],
                ],
                [
                    'sub_event_id'   => $subEventId,
                    'nama_indikator' => $ind['nama_indikator'],
                    'jenis'          => $ind['jenis'],
                ]
            );

            foreach ($ind['keterangans'] as $ket) {
                KeteranganTahap2::firstOrCreate(
                    ['indikator_tahap2_id' => $indikator->id, 'keterangan' => $ket['keterangan']],
                    array_merge(['indikator_tahap2_id' => $indikator->id], $ket)
                );
            }
        }
    }
}