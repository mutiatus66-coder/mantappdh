<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengumuman;

class PengumumanSeeder extends Seeder
{
    public function run(): void
    {
        $pengumuman = [
            [
                'judul'     => 'Pendaftaran INOTEK 2025 Dibuka',
                'deskripsi' => 'Kami dengan bangga mengumumkan bahwa pendaftaran Inovasi Teknologi (INOTEK) tahun 2025 resmi dibuka. Peserta dapat mendaftar melalui portal resmi mulai tanggal 1 Februari 2025 hingga 31 Maret 2025. Kategori yang dilombakan meliputi Kesehatan, Pendidikan, Pertanian & Pangan, dan Teknologi Informasi.',
                'status'    => 'Published',
                'file_path' => null,
            ],
            [
                'judul'     => 'Pengumuman Lolos Seleksi Administrasi INOTEK 2024',
                'deskripsi' => 'Berikut ini adalah daftar peserta yang dinyatakan lolos seleksi administrasi INOTEK 2024. Peserta yang lolos diminta untuk mempersiapkan makalah inovasi sesuai panduan yang telah ditetapkan dan mengumpulkannya paling lambat tanggal 30 April 2024.',
                'status'    => 'Published',
                'file_path' => null,
            ],
            [
                'judul'     => 'Jadwal Presentasi Tahap 2 INODA 2024',
                'deskripsi' => 'Peserta INODA 2024 yang telah dinyatakan lolos ke Tahap 2 diminta untuk memperhatikan jadwal presentasi berikut. Presentasi akan dilaksanakan pada tanggal 10-12 Juli 2024 di Gedung Bapperida Provinsi Jawa Timur.',
                'status'    => 'Published',
                'file_path' => null,
            ],
            [
                'judul'     => 'Pemenang INOTEK 2024',
                'deskripsi' => 'Selamat kepada para pemenang Inovasi Teknologi (INOTEK) 2024. Pengumuman pemenang dan jadwal penyerahan hadiah akan segera diterbitkan. Terima kasih kepada seluruh peserta atas partisipasi dan inovasinya.',
                'status'    => 'Published',
                'file_path' => null,
            ],
            [
                'judul'     => 'Panduan Penulisan Makalah INODA 2025 (Draft)',
                'deskripsi' => 'Dokumen panduan penulisan makalah untuk INODA 2025 masih dalam tahap finalisasi. Panduan ini akan segera dipublikasikan setelah mendapat persetujuan dari panitia.',
                'status'    => 'Draft',
                'file_path' => null,
            ],
        ];

        foreach ($pengumuman as $item) {
            Pengumuman::firstOrCreate(
                ['judul' => $item['judul']],
                $item
            );
        }
    }
}