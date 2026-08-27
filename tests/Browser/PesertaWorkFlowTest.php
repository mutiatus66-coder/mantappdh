<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\SubEvent;

class PesertaWorkFlowTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // membuat contoh file dari magic byte untuk validasi JS
        file_put_contents(__DIR__.'/contoh.pdf', "\x25\x50\x44\x46\x2D\x31\x2E\x34\x0A");
        file_put_contents(__DIR__.'/contoh.jpg', "\xFF\xD8\xFF\xE0\x00\x10\x4A\x46\x49\x46\x00\x01");
    }

    public function testPesertaRealFlow(): void
    {
        $this->browse(function (Browser $browser) {
            $uniqueEmail = 'peserta_' . time() . '@test.com';

            // 1. Buka landing page
            $browser->visit('/')
                    ->pause(1000)
                    ->screenshot('real_1_landing_page');

            // 2. Ke halaman pendaftaran dengan menekan tombol pendaftaran
            $browser->click('.btn-register')
                    ->pause(1000)
                    ->screenshot('real_2_sign_up');

            // 3. Mengisi input pendaftaran
            $browser->type('name', 'Peserta Baru Real')
                    ->type('email', $uniqueEmail)
                    ->type('password', 'Password123!')
                    ->type('password_confirmation', 'Password123!')
                    ->check('captcha_verified') // Centang captcha
                    ->pause(2000) // Tunggu loading captcha 1.5s
                    ->press('Daftar')
                    ->pause(3000)
                    ->screenshot('real_3_registered');

            // 4. Menekan tombol riwayat pada sidebar
            $browser->clickLink('Riwayat')
                    ->pause(2000)
                    ->screenshot('real_4_riwayat_subevent');

            // 5. Menekan kelola usulan
            // Karena kita tidak tahu persis ID tombol, kita akan ambil SubEvent pertama yang aktif
            $browser->clickLink('Kelola Usulan') // Asumsi ada teks ini, jika gagal, fallback ke direct visit
                    ->pause(2000)
                    ->screenshot('real_5_kelola_usulan');

            // 6. Menekan tambah usulan
            $browser->press('Tambah Usulan') // Menekan tombol dengan text Tambah Usulan
                    ->pause(1000)
                    ->screenshot('real_6_modal_tambah');

            // 7. Mengisi Form Langkah 1
            $browser->type('nama_inovasi', 'Inovasi E2E Test')
                    ->type('judul', 'Judul Inovasi Test')
                    ->select('bidang_id') // Pilih option pertama yang tersedia
                    ->type('interaksi', 'Aplikasi Web')
                    ->select('kategori', 'umum')
                    ->type('inovator', 'Instansi Test')
                    ->type('ketua_nama', 'Budi Test')
                    ->type('ketua_email', $uniqueEmail)
                    ->type('ketua_wa', '081234567890')
                    ->type('alamat_ketua', 'Jl. Test No. 123')
                    ->type('ktp', '1234567890123456') // 16 digit
                    ->pause(1000)
                    ->screenshot('real_7_form_step_1')
                    ->press('Selanjutnya')
                    ->pause(1000);

            // Mengisi Form Langkah 2
            $browser->type('latar_belakang', 'Latar Belakang ...')
                    ->type('kondisi_sebelumnya', 'Kondisi Sebelumnya ...')
                    ->type('sasaran_tujuan', 'Sasaran Tujuan ...')
                    ->type('deskripsi', 'Deskripsi ...')
                    ->type('cara_kerja', 'Cara Kerja ...')
                    ->type('keunggulan', 'Keunggulan ...')
                    ->type('hasil_diharapkan', 'Hasil yang Diharapkan ...')
                    ->type('manfaat', 'Manfaat ...')
                    ->type('rencana_berkelanjutan', 'Rencana Berkelanjutan ...')
                    ->pause(1000)
                    ->screenshot('real_8_form_step_2')
                    ->press('Selanjutnya')
                    ->pause(1000);

            // 9. Mengisi Form Langkah 3
            $browser->attach('file_surat_pernyataan', __DIR__.'/contoh.pdf')
                    ->attach('file_proposal', __DIR__.'/contoh.pdf')
                    ->attach('file_gambar', __DIR__.'/contoh.jpg')
                    ->type('link_video', 'https://youtube.com/watch?v=123')
                    ->pause(2000)
                    ->screenshot('real_9_form_step_3')
                    ->press('Simpan Usulan')
                    ->pause(3000)
                    ->screenshot('real_10_usulan_tersimpan');

            // 9.5. Menekan tombol Kirim pada usulan yang baru dibuat
            echo "   -> Menekan tombol Kirim...\n";
            $browser->pause(2000); // Ekstra pause untuk memastikan halaman sudah reload sepenuhnya
            $browser->script(
                "let btns = document.querySelectorAll('.btn-kirim');" .
                "if(btns.length > 0) { let btn = btns[0]; btn.scrollIntoView({block: 'center'}); btn.click(); }"
            );
            $browser->pause(2000);
            $browser->script(
                "let btnOk = document.getElementById('btnOkKirim');" .
                "if(btnOk) { btnOk.scrollIntoView({block: 'center'}); btnOk.click(); }"
            );
            $browser->pause(3000);

            // 8. Kembali (mengklik tombol back/kembali)
            $browser->clickLink('Kembali') // Tombol kembali di usulan.blade.php
                    ->pause(2000)
                    ->screenshot('real_11_kembali_riwayat');

            // 9. Log Out
            // Klik avatar (Asumsi selector '#kt_header_user_menu_toggle' atau kita bisa tembak href /logout)
            // Karena sidebar/header dari template Metronic
            $browser->click('.cursor-pointer.symbol') // Biasanya metronic pakai ini untuk avatar dropdown
                    ->pause(1000)
                    ->clickLink('Sign Out') // Asumsi ada link Sign Out
                    ->pause(2000)
                    ->screenshot('real_12_logged_out');
        });
    }
}
