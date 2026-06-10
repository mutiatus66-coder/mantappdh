<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InovatorSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('inovator')->truncate();

        $now  = now();
        $rows = [
            // ── 1. LOMBA INOTEK 2022 ──────────────────────────────────────
            [1, 'PUSKESMAS NGUJUNG',           'GERDU CETING',                                                'umum'],
            [1, 'Puskesmas Takeran',            'Teknik Bintang (Bimbingan Intensif ASI untuk Anak Cemerlang)', 'umum'],
            [1, 'Puskesmas Plaosan',            'SIAP DESA (Sistem Informasi Akses Pangan Desa)',               'umum'],
            [1, 'Dinas Kesehatan Kab. Magetan', 'SIBADES (Sistem Basis Data Elektronik Stunting)',              'umum'],
            [1, 'RSUD dr. Sayidiman',           'SERASI (Sistem Rekam Medis Terintegrasi)',                     'umum'],
            [1, 'Heru Surya',                   'Tes',                                                          'pelajar'],
            [1, 'DEDY ARIF BUDIAWAN',           'TESAO (Tempat Sampah Otomatis)',                               'pelajar'],
            [1, 'Dwi Tara Romadhoni',           'RenguWi',                                                      'pelajar'],
            [1, 'SMK Negeri 1 Magetan',         'Mesin Pencacah Plastik Tenaga Surya',                          'pelajar'],
            [1, 'SMA Negeri 2 Magetan',         'Biofilter Limbah Rumah Tangga Berbasis Tanaman Eceng Gondok',  'pelajar'],

            // ── 2. LOMBA INOTEK (INOTEK AWARD) 2023 ──────────────────────
            [2, 'Dinas Pertanian Kab. Magetan', 'SIPULAGA (Sistem Informasi Pupuk dan Lahan Pertanian)', 'umum'],
            [2, 'Puskesmas Barat',              'TEMAN SEHAT (Telemedicine Antar Masyarakat Sehat)',     'umum'],
            [2, 'Kelurahan Selosari',           'SATU PINTU (Sistem Administrasi Terpadu Pelayanan)',    'umum'],
            [2, 'Dinas Lingkungan Hidup',       'BANK SAMPAH DIGITAL Kab. Magetan',                     'umum'],
            [2, 'MAN 1 Magetan',                'HIDROPONIK CERDAS Berbasis IoT',                       'pelajar'],
            [2, 'SMAN 1 Parang',                'Alat Pendeteksi Tanah Longsor Berbasis Sensor',         'pelajar'],
            [2, 'SMKN 2 Magetan',               'Kursi Roda Elektrik Kendali Suara untuk Difabel',       'pelajar'],

            // ── 3. PELAPORAN INOVASI DAERAH 2023 ─────────────────────────
            [3, 'BAPPEDA Kab. Magetan',        'MAGETAN SMART CITY Dashboard Terintegrasi',          'umum'],
            [3, 'Dinas Kependudukan & Catpil', 'e-DUKCAPIL Mobile Layanan Adminduk Online',          'umum'],
            [3, 'Dinas Sosial Kab. Magetan',   'SIGAP SOSIAL (Sistem Informasi Gakin Terintegrasi)', 'umum'],

            // ── 4. LOMBA INOVASI DAN TEKNOLOGI 2024 ──────────────────────
            [4, 'RSUD dr. Sayidiman Magetan',   'JERIGEN BEKAS JADI SAFETY BOX "RIKA D\'BOX"',                           'umum'],
            [4, 'Puskesmas Karangrejo',         'INOVASI DETEKSI DINI STUNTING BERBASIS DIGITAL',                        'umum'],
            [4, 'Dinas Pertanian Kab. Magetan', 'AGRO-SMART: Monitoring Lahan Pertanian Berbasis Drone',                 'umum'],
            [4, 'Kelurahan Magetan',            'E-MUSRENBANG: Platform Digital Perencanaan Pembangunan',               'umum'],
            [4, 'BPBD Kab. Magetan',            'SIAGA BENCANA Real-Time Berbasis GIS',                                 'umum'],
            [4, 'Widhi Rahman Hardani',         'Inovasi Apartemen Lele Vertikal dengan Sistem Pemberian Pakan Otomatis','pelajar'],
            [4, 'SMA Negeri 1 Magetan',         'Robot Pemilah Sampah Otomatis Berbasis AI',                             'pelajar'],
            [4, 'MTs Negeri 1 Magetan',         'Pupuk Organik Cair dari Limbah Dapur Berbasis Bioaktivator',            'pelajar'],
            [4, 'SMK Negeri 3 Magetan',         'Smart Greenhouse Pengendali Suhu dan Kelembaban Otomatis',              'pelajar'],

            // ── 5. PELAPORAN INOVASI DAERAH 2024 & INODA AWARD 2025 ──────
            [5, 'Dinas Kominfo Kab. Magetan',    'MAGETAN DIGITAL HUB: Portal Layanan Publik Terpadu',           'umum'],
            [5, 'Dinas Kesehatan Kab. Magetan',  'TELEMEDICINE DESA: Konsultasi Dokter Jarak Jauh',              'umum'],
            [5, 'Dinas PUPR Kab. Magetan',       'e-MONEV INFRASTRUKTUR Real-Time Berbasis GIS',                 'umum'],
            [5, 'BPBD Kab. Magetan',             'DESTANA DIGITAL: Desa Tangguh Bencana Berbasis IoT',           'umum'],
            [5, 'Dinas Pendidikan Kab. Magetan', 'SIMDIK: Sistem Informasi Manajemen Pendidikan Daerah',         'umum'],
            [5, 'Dinas Perdagangan Kab. Magetan','PASAR DIGITAL MAGETAN: Platform UMKM Online',                  'umum'],
            [5, 'SMAN 2 Magetan',                'Biopestisida Alami dari Ekstrak Daun Nimba dan Bawang Putih',  'pelajar'],
            [5, 'MAN 2 Magetan',                 'Aplikasi Belajar Bahasa Jawa Berbasis Gamifikasi',             'pelajar'],
            [5, 'SMKN 1 Magetan',                'Alat Pengering Hasil Panen Tenaga Surya Portabel',             'pelajar'],
            [5, 'SMPN 1 Magetan',                'Media Pembelajaran IPA Interaktif Berbasis Augmented Reality', 'pelajar'],

            // ── 6. PAMERAN INOTEK 2025 ────────────────────────────────────
            [6, 'PT. Magetan Teknologi',  'SmartFarm AI: Pertanian Presisi Berbasis Kecerdasan Buatan', 'umum'],
            [6, 'CV. Inovasi Nusantara',  'AquaMonitor: Sistem Pemantauan Kualitas Air Real-Time',      'umum'],
            [6, 'SMAN 1 Magetan',         'ECOBOT: Robot Penyiram Tanaman Otomatis Bertenaga Surya',    'pelajar'],
            [6, 'SMKN 2 Magetan',         'Tas Pintar Anti-Maling Berbasis GPS dan Fingerprint',        'pelajar'],

            // ── 7. KOMPETISI INOVASI DIGITAL 2025 ─────────────────────────
            [7, 'Startup Magetan Digital', 'WARUNG DIGITAL: Digitalisasi UMKM Pedesaan',             'umum'],
            [7, 'Komunitas Tech Magetan',  'JOGO NDESO: Aplikasi Keamanan Lingkungan Berbasis Warga', 'umum'],
            [7, 'MAN 1 Magetan',           'SiKompas: Sistem Kompas Digital untuk Petani Lokal',      'pelajar'],
        ];

        $insert = array_map(fn($r) => [
            'sub_event_id' => $r[0],
            'inovator'     => $r[1],
            'nama_inovasi' => $r[2],
            'kategori'     => $r[3],
            'created_at'   => $now,
            'updated_at'   => $now,
        ], $rows);

        DB::table('inovator')->insert($insert);

        $this->command->info('InovatorSeeder: ' . count($insert) . ' records inserted.');
    }
}