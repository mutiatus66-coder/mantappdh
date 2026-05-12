<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    private static array $penilai = [
        ['id' => 1, 'nama' => 'Muhammad Fauzi', 'nama_singkat' => 'Muhammad'],
        ['id' => 2, 'nama' => 'Mujiono',         'nama_singkat' => 'Mujiono'],
        ['id' => 3, 'nama' => 'Moch. Hasan',     'nama_singkat' => 'Moch.'],
        ['id' => 4, 'nama' => 'Jatmiko Wibowo',  'nama_singkat' => 'Jatmiko'],
        ['id' => 5, 'nama' => 'Andi Prasetyo',   'nama_singkat' => 'Andi'],
        ['id' => 6, 'nama' => 'Joko Susanto',    'nama_singkat' => 'Joko'],
        ['id' => 7, 'nama' => 'Heru Prasetyo',   'nama_singkat' => 'Heru'],
    ];

    private static array $nominasi = [
        // ── Sub Event 1 — LOMBA INOTEK 2022 ─────────────────────────────────
        1 => [
            ['id' => 101, 'inovator' => 'PUSKESMAS NGUJUNG',           'nama_inovasi' => 'GERDU CETING',                                               'kategori' => 'umum',    'rangking' => 1,    'lolos' => true,  'total_nilai' => 1006.88, 'nilai' => [1 => 264.40, 2 => 44.77,  3 => 253.00, 4 => 55.45, 5 => 147.20, 6 => 41.66,  7 => 200.40]],
            ['id' => 102, 'inovator' => 'Puskesmas Takeran',            'nama_inovasi' => 'Teknik Bintang (Bimbingan Intensif ASI untuk Anak Cemerlang)', 'kategori' => 'umum',  'rangking' => 2,    'lolos' => true,  'total_nilai' => 1053.62, 'nilai' => [1 => 293.20, 2 => 159.20, 3 => 288.00, 4 => 66.77, 5 => 167.00, 6 => null,   7 => 79.45]],
            ['id' => 103, 'inovator' => 'Puskesmas Plaosan',            'nama_inovasi' => 'SIAP DESA (Sistem Informasi Akses Pangan Desa)',               'kategori' => 'umum',   'rangking' => 3,    'lolos' => true,  'total_nilai' => 987.34,  'nilai' => [1 => 241.00, 2 => 138.60, 3 => 270.14, 4 => 61.20, 5 => 155.40, 6 => 52.00,  7 => 69.00]],
            ['id' => 104, 'inovator' => 'Dinas Kesehatan Kab. Magetan', 'nama_inovasi' => 'SIBADES (Sistem Basis Data Elektronik Stunting)',              'kategori' => 'umum',   'rangking' => null, 'lolos' => false, 'total_nilai' => 812.50,  'nilai' => [1 => 210.00, 2 => 98.00,  3 => 200.50, 4 => 55.00, 5 => 110.00, 6 => 80.00,  7 => 59.00]],
            ['id' => 105, 'inovator' => 'RSUD dr. Sayidiman',           'nama_inovasi' => 'SERASI (Sistem Rekam Medis Terintegrasi)',                     'kategori' => 'umum',   'rangking' => null, 'lolos' => false, 'total_nilai' => 760.00,  'nilai' => [1 => 190.00, 2 => 85.00,  3 => 185.00, 4 => 50.00, 5 => 100.00, 6 => 90.00,  7 => 60.00]],
            ['id' => 106, 'inovator' => 'Heru Surya',                   'nama_inovasi' => 'Tes',                                                          'kategori' => 'pelajar','rangking' => null, 'lolos' => false, 'total_nilai' => 0.00,    'nilai' => [1 => null,   2 => null,   3 => null,   4 => null,  5 => null,   6 => null,   7 => null]],
            ['id' => 107, 'inovator' => 'DEDY ARIF BUDIAWAN',           'nama_inovasi' => 'TESAO (Tempat Sampah Otomatis)',                               'kategori' => 'pelajar','rangking' => 1,    'lolos' => true,  'total_nilai' => 877.93,  'nilai' => [1 => 47.85,  2 => 43.30,  3 => 245.00, 4 => 59.38, 5 => 194.00, 6 => 100.40, 7 => 188.00]],
            ['id' => 108, 'inovator' => 'Dwi Tara Romadhoni',           'nama_inovasi' => 'RenguWi',                                                      'kategori' => 'pelajar','rangking' => 2,    'lolos' => true,  'total_nilai' => 613.45,  'nilai' => [1 => 46.92,  2 => 41.77,  3 => 196.00, 4 => 66.78, 5 => 200.60, 6 => null,   7 => 61.38]],
            ['id' => 109, 'inovator' => 'SMK Negeri 1 Magetan',         'nama_inovasi' => 'Mesin Pencacah Plastik Tenaga Surya',                          'kategori' => 'pelajar','rangking' => 3,    'lolos' => true,  'total_nilai' => 554.20,  'nilai' => [1 => 31.54,  2 => 195.00, 3 => 230.00, 4 => null,  5 => null,   6 => 61.88,  7 => 35.78]],
            ['id' => 110, 'inovator' => 'SMA Negeri 2 Magetan',         'nama_inovasi' => 'Biofilter Limbah Rumah Tangga Berbasis Tanaman Eceng Gondok',  'kategori' => 'pelajar','rangking' => null, 'lolos' => false, 'total_nilai' => 420.00,  'nilai' => [1 => 60.00,  2 => 55.00,  3 => 120.00, 4 => 45.00, 5 => 80.00,  6 => 30.00,  7 => 30.00]],
        ],

        // ── Sub Event 2 — LOMBA INOTEK 2023 ─────────────────────────────────
        2 => [
            ['id' => 201, 'inovator' => 'Dinas Pertanian Kab. Magetan', 'nama_inovasi' => 'SIPULAGA (Sistem Informasi Pupuk dan Lahan Pertanian)',  'kategori' => 'umum',    'rangking' => 1,    'lolos' => true,  'total_nilai' => 1120.40, 'nilai' => [1 => 300.00, 2 => 170.40, 3 => 290.00, 4 => 80.00, 5 => 160.00, 6 => 60.00, 7 => 60.00]],
            ['id' => 202, 'inovator' => 'Puskesmas Barat',              'nama_inovasi' => 'TEMAN SEHAT (Telemedicine Antar Masyarakat Sehat)',      'kategori' => 'umum',    'rangking' => 2,    'lolos' => true,  'total_nilai' => 1045.80, 'nilai' => [1 => 280.00, 2 => 155.80, 3 => 270.00, 4 => 70.00, 5 => 150.00, 6 => 60.00, 7 => 60.00]],
            ['id' => 203, 'inovator' => 'Kelurahan Selosari',           'nama_inovasi' => 'SATU PINTU (Sistem Administrasi Terpadu Pelayanan)',     'kategori' => 'umum',    'rangking' => null, 'lolos' => false, 'total_nilai' => 880.20,  'nilai' => [1 => 230.00, 2 => 120.20, 3 => 220.00, 4 => 60.00, 5 => 130.00, 6 => 60.00, 7 => 60.00]],
            ['id' => 204, 'inovator' => 'Dinas Lingkungan Hidup',       'nama_inovasi' => 'BANK SAMPAH DIGITAL Kab. Magetan',                      'kategori' => 'umum',    'rangking' => null, 'lolos' => false, 'total_nilai' => 750.00,  'nilai' => [1 => 190.00, 2 => 100.00, 3 => 185.00, 4 => 55.00, 5 => 110.00, 6 => 55.00, 7 => 55.00]],
            ['id' => 205, 'inovator' => 'MAN 1 Magetan',                'nama_inovasi' => 'HIDROPONIK CERDAS Berbasis IoT',                        'kategori' => 'pelajar', 'rangking' => 1,    'lolos' => true,  'total_nilai' => 965.50,  'nilai' => [1 => 260.00, 2 => 145.50, 3 => 250.00, 4 => 70.00, 5 => 140.00, 6 => 50.00, 7 => 50.00]],
            ['id' => 206, 'inovator' => 'SMAN 1 Parang',                'nama_inovasi' => 'Alat Pendeteksi Tanah Longsor Berbasis Sensor',          'kategori' => 'pelajar', 'rangking' => 2,    'lolos' => true,  'total_nilai' => 890.30,  'nilai' => [1 => 240.00, 2 => 130.30, 3 => 230.00, 4 => 60.00, 5 => 130.00, 6 => 50.00, 7 => 50.00]],
            ['id' => 207, 'inovator' => 'SMKN 2 Magetan',               'nama_inovasi' => 'Kursi Roda Elektrik Kendali Suara untuk Difabel',        'kategori' => 'pelajar', 'rangking' => null, 'lolos' => false, 'total_nilai' => 710.00,  'nilai' => [1 => 180.00, 2 => 110.00, 3 => 175.00, 4 => 55.00, 5 => 110.00, 6 => 40.00, 7 => 40.00]],
        ],

        // ── Sub Event 3 — PELAPORAN INOVASI DAERAH 2023 ──────────────────────
        3 => [
            ['id' => 301, 'inovator' => 'BAPPEDA Kab. Magetan',        'nama_inovasi' => 'MAGETAN SMART CITY Dashboard Terintegrasi',         'kategori' => 'umum', 'rangking' => 1,    'lolos' => true,  'total_nilai' => 1198.00, 'nilai' => [1 => 320.00, 2 => 178.00, 3 => 310.00, 4 => 90.00, 5 => 170.00, 6 => 65.00, 7 => 65.00]],
            ['id' => 302, 'inovator' => 'Dinas Kependudukan & Catpil', 'nama_inovasi' => 'e-DUKCAPIL Mobile Layanan Adminduk Online',         'kategori' => 'umum', 'rangking' => 2,    'lolos' => true,  'total_nilai' => 1055.50, 'nilai' => [1 => 285.50, 2 => 160.00, 3 => 275.00, 4 => 75.00, 5 => 155.00, 6 => 55.00, 7 => 50.00]],
            ['id' => 303, 'inovator' => 'Dinas Sosial Kab. Magetan',   'nama_inovasi' => 'SIGAP SOSIAL (Sistem Informasi Gakin Terintegrasi)', 'kategori' => 'umum', 'rangking' => null, 'lolos' => false, 'total_nilai' => 830.00,  'nilai' => [1 => 215.00, 2 => 115.00, 3 => 205.00, 4 => 65.00, 5 => 120.00, 6 => 55.00, 7 => 55.00]],
        ],

        // ── Sub Event 4 — LOMBA INOVASI DAN TEKNOLOGI 2024 ──────────────────
        4 => [
            ['id' => 401, 'inovator' => 'RSUD dr. Sayidiman Magetan',  'nama_inovasi' => 'JERIGEN BEKAS JADI SAFETY BOX "RIKA D\'BOX"',            'kategori' => 'umum',    'rangking' => null, 'lolos' => true,  'total_nilai' => 430.8,  'nilai' => [1 => null,  2 => 72.4,  3 => 65.8,  4 => 69.8,  5 => 70.0,  6 => 72.4,  7 => 80.4]],
            ['id' => 402, 'inovator' => 'Puskesmas Karangrejo',         'nama_inovasi' => 'INOVASI DETEKSI DINI STUNTING BERBASIS DIGITAL',         'kategori' => 'umum',    'rangking' => null, 'lolos' => false, 'total_nilai' => 0,      'nilai' => [1 => null,  2 => null,  3 => null,  4 => null,  5 => null,  6 => null,  7 => null]],
            ['id' => 403, 'inovator' => 'Dinas Pertanian Kab. Magetan','nama_inovasi' => 'AGRO-SMART: Monitoring Lahan Pertanian Berbasis Drone',  'kategori' => 'umum',    'rangking' => null, 'lolos' => false, 'total_nilai' => 512.60, 'nilai' => [1 => 80.60, 2 => 72.00, 3 => null,  4 => 88.00, 5 => 90.00, 6 => 92.00, 7 => 90.00]],
            ['id' => 404, 'inovator' => 'Kelurahan Magetan',            'nama_inovasi' => 'E-MUSRENBANG: Platform Digital Perencanaan Pembangunan', 'kategori' => 'umum',    'rangking' => null, 'lolos' => false, 'total_nilai' => 388.40, 'nilai' => [1 => null,  2 => 55.40, 3 => 62.00, 4 => 58.00, 5 => 70.00, 6 => null,  7 => 143.00]],
            ['id' => 405, 'inovator' => 'BPBD Kab. Magetan',           'nama_inovasi' => 'SIAGA BENCANA Real-Time Berbasis GIS',                   'kategori' => 'umum',    'rangking' => null, 'lolos' => false, 'total_nilai' => 0,      'nilai' => [1 => null,  2 => null,  3 => null,  4 => null,  5 => null,  6 => null,  7 => null]],
            ['id' => 406, 'inovator' => 'Widhi Rahman Hardani',         'nama_inovasi' => 'Inovasi Apartemen Lele Vertikal dengan Sistem Pemberian Pakan Otomatis', 'kategori' => 'pelajar', 'rangking' => null, 'lolos' => true,  'total_nilai' => 406.8,  'nilai' => [1 => null, 2 => 69.0,  3 => 72.8,  4 => 59.0,  5 => 73.6,  6 => 64.0,  7 => 68.4]],
            ['id' => 407, 'inovator' => 'SMA Negeri 1 Magetan',         'nama_inovasi' => 'Robot Pemilah Sampah Otomatis Berbasis AI',               'kategori' => 'pelajar', 'rangking' => null, 'lolos' => false, 'total_nilai' => 0,      'nilai' => [1 => null,  2 => null,  3 => null,  4 => null,  5 => null,  6 => null,  7 => null]],
            ['id' => 408, 'inovator' => 'MTs Negeri 1 Magetan',         'nama_inovasi' => 'Pupuk Organik Cair dari Limbah Dapur Berbasis Bioaktivator', 'kategori' => 'pelajar', 'rangking' => null, 'lolos' => false, 'total_nilai' => 355.20, 'nilai' => [1 => null, 2 => 55.20, 3 => 60.00, 4 => 50.00, 5 => 62.00, 6 => null,  7 => 128.00]],
            ['id' => 409, 'inovator' => 'SMK Negeri 3 Magetan',         'nama_inovasi' => 'Smart Greenhouse Pengendali Suhu dan Kelembaban Otomatis', 'kategori' => 'pelajar', 'rangking' => null, 'lolos' => false, 'total_nilai' => 0,      'nilai' => [1 => null,  2 => null,  3 => null,  4 => null,  5 => null,  6 => null,  7 => null]],
        ],

        // ── Sub Event 5 — PELAPORAN INOVASI DAERAH 2024 ──────────────────────
        5 => [
            ['id' => 501, 'inovator' => 'Dinas Kominfo Kab. Magetan',    'nama_inovasi' => 'MAGETAN DIGITAL HUB: Portal Layanan Publik Terpadu',    'kategori' => 'umum',    'rangking' => 1,    'lolos' => true,  'total_nilai' => 1245.00, 'nilai' => [1 => 335.00, 2 => 185.00, 3 => 320.00, 4 => 95.00, 5 => 175.00, 6 => 70.00, 7 => 65.00]],
            ['id' => 502, 'inovator' => 'Dinas Kesehatan Kab. Magetan',  'nama_inovasi' => 'TELEMEDICINE DESA: Konsultasi Dokter Jarak Jauh',        'kategori' => 'umum',    'rangking' => 2,    'lolos' => true,  'total_nilai' => 1100.50, 'nilai' => [1 => 295.50, 2 => 165.00, 3 => 280.00, 4 => 85.00, 5 => 160.00, 6 => 60.00, 7 => 55.00]],
            ['id' => 503, 'inovator' => 'Dinas PUPR Kab. Magetan',       'nama_inovasi' => 'e-MONEV INFRASTRUKTUR Real-Time Berbasis GIS',          'kategori' => 'umum',    'rangking' => 3,    'lolos' => true,  'total_nilai' => 980.75,  'nilai' => [1 => 260.75, 2 => 145.00, 3 => 255.00, 4 => 75.00, 5 => 145.00, 6 => 55.00, 7 => 45.00]],
            ['id' => 504, 'inovator' => 'BPBD Kab. Magetan',             'nama_inovasi' => 'DESTANA DIGITAL: Desa Tangguh Bencana Berbasis IoT',    'kategori' => 'umum',    'rangking' => null, 'lolos' => false, 'total_nilai' => 845.00,  'nilai' => [1 => 225.00, 2 => 120.00, 3 => 210.00, 4 => 70.00, 5 => 125.00, 6 => 50.00, 7 => 45.00]],
            ['id' => 505, 'inovator' => 'Dinas Pendidikan Kab. Magetan', 'nama_inovasi' => 'SIMDIK: Sistem Informasi Manajemen Pendidikan Daerah',  'kategori' => 'umum',    'rangking' => null, 'lolos' => false, 'total_nilai' => 720.00,  'nilai' => [1 => 190.00, 2 => 105.00, 3 => 180.00, 4 => 60.00, 5 => 110.00, 6 => 40.00, 7 => 35.00]],
            ['id' => 506, 'inovator' => 'Dinas Perdagangan Kab. Magetan','nama_inovasi' => 'PASAR DIGITAL MAGETAN: Platform UMKM Online',          'kategori' => 'umum',    'rangking' => null, 'lolos' => false, 'total_nilai' => 0,       'nilai' => [1 => null,   2 => null,   3 => null,   4 => null,  5 => null,   6 => null,  7 => null]],
            ['id' => 507, 'inovator' => 'SMAN 2 Magetan',                'nama_inovasi' => 'Biopestisida Alami dari Ekstrak Daun Nimba dan Bawang Putih', 'kategori' => 'pelajar', 'rangking' => 1,    'lolos' => true,  'total_nilai' => 1010.20, 'nilai' => [1 => 270.20, 2 => 150.00, 3 => 265.00, 4 => 80.00, 5 => 150.00, 6 => 50.00, 7 => 45.00]],
            ['id' => 508, 'inovator' => 'MAN 2 Magetan',                 'nama_inovasi' => 'Aplikasi Belajar Bahasa Jawa Berbasis Gamifikasi',       'kategori' => 'pelajar', 'rangking' => 2,    'lolos' => true,  'total_nilai' => 888.60,  'nilai' => [1 => 238.60, 2 => 130.00, 3 => 230.00, 4 => 70.00, 5 => 130.00, 6 => 45.00, 7 => 45.00]],
            ['id' => 509, 'inovator' => 'SMKN 1 Magetan',                'nama_inovasi' => 'Alat Pengering Hasil Panen Tenaga Surya Portabel',       'kategori' => 'pelajar', 'rangking' => null, 'lolos' => false, 'total_nilai' => 660.00,  'nilai' => [1 => 175.00, 2 => 100.00, 3 => 170.00, 4 => 55.00, 5 => 100.00, 6 => 30.00, 7 => 30.00]],
            ['id' => 510, 'inovator' => 'SMPN 1 Magetan',                'nama_inovasi' => 'Media Pembelajaran IPA Interaktif Berbasis Augmented Reality', 'kategori' => 'pelajar', 'rangking' => null, 'lolos' => false, 'total_nilai' => 0,       'nilai' => [1 => null,   2 => null,   3 => null,   4 => null,  5 => null,   6 => null,  7 => null]],
        ],

        // ── Sub Event 6 — PAMERAN INOTEK 2025 ───────────────────────────────
        6 => [
            ['id' => 601, 'inovator' => 'PT. Magetan Teknologi', 'nama_inovasi' => 'SmartFarm AI: Pertanian Presisi Berbasis Kecerdasan Buatan', 'kategori' => 'umum',    'rangking' => null, 'lolos' => false, 'total_nilai' => 0, 'nilai' => [1 => null, 2 => null, 3 => null, 4 => null, 5 => null, 6 => null, 7 => null]],
            ['id' => 602, 'inovator' => 'CV. Inovasi Nusantara', 'nama_inovasi' => 'AquaMonitor: Sistem Pemantauan Kualitas Air Real-Time',      'kategori' => 'umum',    'rangking' => null, 'lolos' => false, 'total_nilai' => 0, 'nilai' => [1 => null, 2 => null, 3 => null, 4 => null, 5 => null, 6 => null, 7 => null]],
            ['id' => 603, 'inovator' => 'SMAN 1 Magetan',        'nama_inovasi' => 'ECOBOT: Robot Penyiram Tanaman Otomatis Bertenaga Surya',    'kategori' => 'pelajar', 'rangking' => null, 'lolos' => false, 'total_nilai' => 0, 'nilai' => [1 => null, 2 => null, 3 => null, 4 => null, 5 => null, 6 => null, 7 => null]],
            ['id' => 604, 'inovator' => 'SMKN 2 Magetan',        'nama_inovasi' => 'Tas Pintar Anti-Maling Berbasis GPS dan Fingerprint',        'kategori' => 'pelajar', 'rangking' => null, 'lolos' => false, 'total_nilai' => 0, 'nilai' => [1 => null, 2 => null, 3 => null, 4 => null, 5 => null, 6 => null, 7 => null]],
        ],

        // ── Sub Event 7 — KOMPETISI INOVASI DIGITAL 2025 ────────────────────
        7 => [
            ['id' => 701, 'inovator' => 'Startup Magetan Digital', 'nama_inovasi' => 'WARUNG DIGITAL: Digitalisasi UMKM Pedesaan',             'kategori' => 'umum',    'rangking' => null, 'lolos' => false, 'total_nilai' => 0, 'nilai' => [1 => null, 2 => null, 3 => null, 4 => null, 5 => null, 6 => null, 7 => null]],
            ['id' => 702, 'inovator' => 'Komunitas Tech Magetan',  'nama_inovasi' => 'JOGO NDESO: Aplikasi Keamanan Lingkungan Berbasis Warga', 'kategori' => 'umum',    'rangking' => null, 'lolos' => false, 'total_nilai' => 0, 'nilai' => [1 => null, 2 => null, 3 => null, 4 => null, 5 => null, 6 => null, 7 => null]],
            ['id' => 703, 'inovator' => 'MAN 1 Magetan',           'nama_inovasi' => 'SiKompas: Sistem Kompas Digital untuk Petani Lokal',     'kategori' => 'pelajar', 'rangking' => null, 'lolos' => false, 'total_nilai' => 0, 'nilai' => [1 => null, 2 => null, 3 => null, 4 => null, 5 => null, 6 => null, 7 => null]],
        ],
    ];

    private function getSubEvents(): array
    {
        return session('sub_events', []);
    }

    private function getNominasiSplit(int $id): array
    {
        $all = self::$nominasi[$id] ?? [];

        return [
            'umum'    => array_values(array_filter($all, fn($n) => $n['kategori'] === 'umum')),
            'pelajar' => array_values(array_filter($all, fn($n) => $n['kategori'] === 'pelajar')),
        ];
    }

    // ── Tahap 1 ───────────────────────────────────────────────────────────────

    public function tahap1Index()
    {
        $subEvents    = $this->getSubEvents();
        $nominasiData = [];

        foreach ($subEvents as $se) {
            $nominasiData[$se['id']] = self::$nominasi[$se['id']] ?? [];
        }

        return view('master.penilaian.tahap1.index', compact('subEvents', 'nominasiData'));
    }

    public function tahap1Show(int $id)
    {
        $subEvent = collect($this->getSubEvents())->firstWhere('id', $id);
        abort_unless($subEvent, 404);

        ['umum' => $nominasiUmum, 'pelajar' => $nominasiPelajar] = $this->getNominasiSplit($id);

        return view('master.penilaian.tahap1.show', [
            'subEvent'        => $subEvent,
            'nominasiUmum'    => $nominasiUmum,
            'nominasiPelajar' => $nominasiPelajar,
            'penilai'         => self::$penilai,
        ]);
    }

    public function tahap1Simpan(Request $request, int $id)
    {
        $request->validate([
            'kategori' => 'required|in:umum,pelajar',
            'ids'      => 'array',
            'ids.*'    => 'integer',
        ]);

        session(['tahap1_lolos_' . $id . '_' . $request->kategori => $request->ids ?? []]);

        return response()->json(['success' => true]);
    }

    // ── Tahap 2 ───────────────────────────────────────────────────────────────

    public function tahap2Index()
    {
        $subEvents    = $this->getSubEvents();
        $nominasiData = [];

        foreach ($subEvents as $se) {
            $nominasiData[$se['id']] = self::$nominasi[$se['id']] ?? [];
        }

        return view('master.penilaian.tahap2.index', compact('subEvents', 'nominasiData'));
    }

    public function tahap2Show(int $id)
    {
        $subEvent = collect($this->getSubEvents())->firstWhere('id', $id);
        abort_unless($subEvent, 404);

        ['umum' => $nominasiUmum, 'pelajar' => $nominasiPelajar] = $this->getNominasiSplit($id);

        return view('master.penilaian.tahap2.show', [
            'subEvent'        => $subEvent,
            'nominasiUmum'    => $nominasiUmum,
            'nominasiPelajar' => $nominasiPelajar,
            'penilai'         => self::$penilai,
        ]);
    }
}