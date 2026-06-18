<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\SubEvent;
use App\Models\Bidang;
use App\Models\Usulan;
use App\Models\User;

class UsulanSeeder extends Seeder
{
    public function run(): void
    {
        $subEvent = SubEvent::with('event')->orderBy('tahun', 'desc')->first();

        if (!$subEvent) {
            $this->command->warn('UsulanSeeder: tidak ada SubEvent. Jalankan SubEventSeeder & EventSeeder dulu.');
            return;
        }

        $bidang = Bidang::query()->where('sub_event_id', $subEvent->id)
            ->where('status', 'aktif')
            ->first();

        if (!$bidang) {
            $this->command->warn('UsulanSeeder: tidak ada Bidang untuk SubEvent ini. Jalankan BidangSeeder dulu.');
            return;
        }

        // Ambil user peserta pertama yang ada di DB (prioritas peserta real, bukan dummy).
        // Kalau belum ada sama sekali, buat satu dummy sebagai fallback.
        $peserta = User::query()->where('hak_akses', 'peserta')
            ->where('status', 'aktif')
            ->orderBy('id')
            ->first();

        if (!$peserta) {
            $peserta = User::firstOrCreate(
                ['email' => 'peserta.dummy@example.com'],
                [
                    'name'      => 'pesertadummy',
                    'nama'      => 'Peserta Dummy',
                    'email'     => 'peserta.dummy@example.com',
                    'password'  => Hash::make('password'),
                    'hak_akses' => 'peserta',
                    'status'    => 'aktif',
                ]
            );
        }

        $base = [
            'sub_event_id' => $subEvent->id,
            'bidang_id'    => $bidang->id,
            'interaksi'    => 'Teknologi Tepat Guna',
            'alamat_ketua' => 'Jl. Diponegoro No. 1, Magetan',
            'ktp'          => '3520000000000001',
            'kategori'     => 'umum',
        ];

        $usulans = [
            array_merge($base, [
                'judul'        => 'Sistem Pemantauan Tanah Pertanian Berbasis Sensor',
                'inovator'     => 'Kelompok Tani Maju Bersama',
                'nama_inovasi' => 'AgroSense',
                'nama_tim'     => 'Tim AgroSense',
                'ketua_nama'   => 'Budi Prasetyo',
                'ketua_email'  => 'budi.prasetyo@email.com',
                'ketua_wa'     => '081234567890',
                'ktp'          => '3520000000000001',
                'interaksi'    => 'Pertanian & Pangan',
                'status'       => 'Melengkapi Data',
                'is_submitted' => false,
                'deskripsi'    => 'Sistem sensor IoT untuk memantau kelembapan dan nutrisi tanah secara real-time.',
                'latar_belakang'        => 'Petani kesulitan mengetahui kondisi tanah secara akurat tanpa alat ukur mahal.',
                'kondisi_sebelumnya'    => 'Petani mengandalkan perkiraan manual yang sering tidak tepat.',
                'sasaran_tujuan'        => 'Membantu petani memantau kondisi tanah dengan mudah dan murah.',
                'cara_kerja'            => 'Sensor dipasang di lahan, data dikirim ke aplikasi mobile via WiFi.',
                'keunggulan'            => 'Biaya rendah, mudah digunakan, data real-time.',
                'hasil_diharapkan'      => 'Hasil panen meningkat 20% karena pengelolaan lahan lebih tepat.',
                'manfaat'               => 'Meningkatkan produktivitas petani dan efisiensi penggunaan pupuk.',
                'rencana_berkelanjutan' => 'Dikembangkan menjadi jaringan sensor seluruh desa.',
            ]),

            array_merge($base, [
                'judul'        => 'Aplikasi Telemedicine untuk Daerah Terpencil',
                'inovator'     => 'Puskesmas Kecamatan Barat',
                'nama_inovasi' => 'TeleMed Rural',
                'nama_tim'     => 'Tim Kesehatan Digital',
                'ketua_nama'   => 'dr. Sari Indah',
                'ketua_email'  => 'sari.indah@email.com',
                'ketua_wa'     => '082345678901',
                'ktp'          => '3520000000000002',
                'interaksi'    => 'Kesehatan Masyarakat',
                'status'       => 'Melengkapi Data',
                'is_submitted' => false,
                'deskripsi'    => 'Aplikasi konsultasi dokter jarak jauh untuk warga daerah terpencil.',
                'latar_belakang'        => 'Warga daerah terpencil sulit mengakses layanan kesehatan karena jarak dan biaya.',
                'kondisi_sebelumnya'    => 'Pasien harus menempuh jarak jauh hanya untuk konsultasi ringan.',
                'sasaran_tujuan'        => 'Menyediakan akses konsultasi medis online yang mudah dan terjangkau.',
                'cara_kerja'            => 'Pasien video call dokter melalui aplikasi, resep dikirim digital ke apotek terdekat.',
                'keunggulan'            => 'Menghemat waktu dan biaya perjalanan, respons cepat.',
                'hasil_diharapkan'      => 'Angka keterlambatan penanganan medis berkurang 50%.',
                'manfaat'               => 'Meningkatkan akses layanan kesehatan bagi masyarakat terpencil.',
                'rencana_berkelanjutan' => 'Integrasi dengan BPJS dan Dinas Kesehatan Kabupaten.',
            ]),

            array_merge($base, [
                'judul'        => 'Pengolahan Sampah Plastik Menjadi Bahan Bakar',
                'inovator'     => 'SMKN 1 Magetan',
                'nama_inovasi' => 'EcoFuel',
                'nama_tim'     => 'Tim Green Energy SMKN 1',
                'ketua_nama'   => 'Ahmad Rizki',
                'ketua_email'  => 'ahmad.rizki@email.com',
                'ketua_wa'     => '083456789012',
                'ktp'          => '3520000000000003',
                'kategori'     => 'pelajar',
                'asal_sekolah' => 'SMKN 1 Magetan',
                'nama_guru'    => 'Drs. Suprapto, M.Pd.',
                'interaksi'    => 'Energi & Lingkungan',
                'status'       => 'Sedang Dinilai',
                'is_submitted' => true,
                'deskripsi'    => 'Alat pirolisis sederhana pengolah sampah plastik menjadi bahan bakar alternatif.',
                'latar_belakang'        => 'Volume sampah plastik di Magetan terus meningkat dan sulit terurai.',
                'kondisi_sebelumnya'    => 'Sampah plastik dibakar terbuka yang mencemari udara.',
                'sasaran_tujuan'        => 'Mengubah sampah plastik menjadi bahan bakar yang dapat digunakan kembali.',
                'cara_kerja'            => 'Plastik dipanaskan dalam reaktor tertutup, uap yang dihasilkan dikondensasi menjadi BBM.',
                'keunggulan'            => 'Biaya produksi rendah, mengurangi sampah sekaligus menghasilkan energi.',
                'hasil_diharapkan'      => '1 kg plastik menghasilkan 0.8 liter bahan bakar setara solar.',
                'manfaat'               => 'Mengurangi pencemaran lingkungan dan menyediakan energi alternatif murah.',
                'rencana_berkelanjutan' => 'Dikembangkan skala industri kecil bersama BUMDes.',
            ]),

            array_merge($base, [
                'judul'        => 'Alat Pengering Hasil Panen Tenaga Surya',
                'inovator'     => 'UD. Surya Tani',
                'nama_inovasi' => 'SolDry',
                'nama_tim'     => 'Tim SolDry',
                'ketua_nama'   => 'Wahyu Setiawan',
                'ketua_email'  => 'wahyu.setiawan@email.com',
                'ketua_wa'     => '084567890123',
                'ktp'          => '3520000000000004',
                'interaksi'    => 'Pertanian & Pangan',
                'status'       => 'Sedang Dinilai',
                'is_submitted' => true,
                'deskripsi'    => 'Alat pengering hasil panen berbasis panel surya untuk menggantikan pengeringan konvensional.',
                'latar_belakang'        => 'Petani sering mengalami gagal panen pascapanen karena proses pengeringan bergantung cuaca.',
                'kondisi_sebelumnya'    => 'Pengeringan dilakukan di bawah sinar matahari langsung, rentan hujan dan debu.',
                'sasaran_tujuan'        => 'Menyediakan alat pengering mandiri yang efisien dan tidak bergantung cuaca.',
                'cara_kerja'            => 'Panel surya menggerakkan blower dan elemen pemanas di ruang pengering tertutup.',
                'keunggulan'            => 'Hemat energi, higienis, kapasitas besar, tidak bergantung cuaca.',
                'hasil_diharapkan'      => 'Waktu pengeringan berkurang 60%, kualitas hasil panen lebih terjaga.',
                'manfaat'               => 'Meningkatkan kualitas dan nilai jual hasil panen petani.',
                'rencana_berkelanjutan' => 'Diproduksi massal melalui koperasi tani dengan subsidi pemerintah daerah.',
            ]),

            array_merge($base, [
                'judul'        => 'Media Pembelajaran Interaktif Berbasis Gamifikasi',
                'inovator'     => 'SMA Negeri 3 Magetan',
                'nama_inovasi' => 'EduPlay',
                'nama_tim'     => 'Tim EduPlay SMA 3',
                'ketua_nama'   => 'Citra Dewi',
                'ketua_email'  => 'citra.dewi@email.com',
                'ketua_wa'     => '085678901234',
                'ktp'          => '3520000000000005',
                'kategori'     => 'pelajar',
                'asal_sekolah' => 'SMA Negeri 3 Magetan',
                'nama_guru'    => 'Ibu Ratna Sari, S.Pd.',
                'interaksi'    => 'Teknologi Pendidikan',
                'status'       => 'Selesai',
                'is_submitted' => true,
                'deskripsi'    => 'Platform belajar berbasis game yang membuat siswa lebih antusias belajar.',
                'latar_belakang'        => 'Siswa kurang termotivasi belajar dengan metode konvensional yang monoton.',
                'kondisi_sebelumnya'    => 'Proses belajar membosankan, nilai rata-rata siswa rendah.',
                'sasaran_tujuan'        => 'Meningkatkan motivasi dan hasil belajar siswa melalui pendekatan gamifikasi.',
                'cara_kerja'            => 'Materi pelajaran dikemas dalam misi dan level game, siswa mendapat poin dan badge.',
                'keunggulan'            => 'Menyenangkan, adaptif sesuai kemampuan siswa, bisa diakses dari HP.',
                'hasil_diharapkan'      => 'Nilai rata-rata kelas meningkat 30% dalam satu semester.',
                'manfaat'               => 'Menciptakan generasi yang gemar belajar dan melek teknologi.',
                'rencana_berkelanjutan' => 'Dikembangkan menjadi platform resmi sekolah-sekolah di Kabupaten Magetan.',
            ]),
        ];

        foreach ($usulans as $item) {
            $item['user_id'] = $peserta->id;

            Usulan::firstOrCreate(
                [
                    'sub_event_id' => $item['sub_event_id'],
                    'nama_inovasi' => $item['nama_inovasi'],
                ],
                $item
            );
        }

        $this->command->info('UsulanSeeder: ' . count($usulans) . ' usulan berhasil di-seed untuk SubEvent "' . $subEvent->sub_event . '" (' . $subEvent->tahun . ').');
        $this->command->info('Semua usulan di-assign ke user: ' . $peserta->nama . ' (id=' . $peserta->id . ', email=' . $peserta->email . ')');
    }
}