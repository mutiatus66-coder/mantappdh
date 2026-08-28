<?php

namespace Tests\Browser\Admin;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Facebook\WebDriver\WebDriverBy;

class Test1_MasterTest extends DuskTestCase
{
    private function loginAdmin(Browser $browser): void
    {
        $browser->visit('http://127.0.0.1:8000/')
                ->pause(1000)
                ->clickLink('Login')
                ->pause(2000)
                ->type('email', 'admin@admin.com')
                ->type('password', 'password')
                ->click('button[type="submit"]')
                ->pause(3000)
                ->assertPathIs('/index');
    }

    public function test_master_crud_features(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080);
            $waktu = time();

            echo "[1] MASTER DATA\n";
            $this->loginAdmin($browser);

            // TEST THEME (DARK / LIGHT)
            echo "   -> Test Tema Gelap & Terang...\n";
            $browser->script("let darkBtn = document.querySelector('[data-kt-element=\"mode\"][data-kt-value=\"dark\"]'); if(darkBtn) darkBtn.click();");
            $browser->pause(1500);
            $browser->script("let lightBtn = document.querySelector('[data-kt-element=\"mode\"][data-kt-value=\"light\"]'); if(lightBtn) lightBtn.click();");
            $browser->pause(1500);

            // 1. EVENT
            echo "   -> Event: Tambah, Ubah, Hapus...\n";
            $browser->click('a.ri-menu-item[href="/event"]')->pause(2000);
            $browser->click('#btnTambahEvent')->pause(800)
                    ->type('#inputNamaEvent', "Event $waktu")
                    ->select('#inputJenis', 'INOTEK')
                    ->click('#btnSimpanEvent')->pause(2000);
            $browser->script("let btns = document.querySelectorAll('.btn-edit-event'); if(btns.length) { let btn = btns[btns.length-1]; btn.scrollIntoView({block:'center'}); btn.click(); }");
            $browser->pause(800)->clear('#inputNamaEvent')->type('#inputNamaEvent', "Event Edit $waktu")->click('#btnSimpanEvent')->pause(2000);
            $browser->script("let btns = document.querySelectorAll('.btn-hapus-event'); if(btns.length) { let btn = btns[btns.length-1]; btn.scrollIntoView({block:'center'}); btn.click(); }");
            $browser->pause(800)->click('#btnHapusEvent')->pause(2000);

            // 2. SUB EVENT
            echo "   -> Sub Event: Tambah, Ubah, Hapus...\n";
            $browser->click('a.ri-menu-item[href="/sub-event"]')->pause(2000);
            $browser->click('#btnTambahSubEvent')->pause(800)
                    ->type('#seTahun', date('Y') + 1);
            $browser->script("let sel = document.getElementById('seEvent'); if(sel && sel.options.length > 1) sel.selectedIndex = 1;");
            $browser->type('#seSubEvent', "Sub Event $waktu")
                    ->type('#seKategori', 'Kategori Test')
                    ->type('#seMulai', '2025-01-01')
                    ->type('#seBerakhir', '2025-12-31')
                    ->click('#btnSimpanSE')->pause(2000);
            $browser->script("let btns = document.querySelectorAll('.btn-hapus-se'); if(btns.length) { let btn = btns[btns.length-1]; btn.scrollIntoView({block:'center'}); btn.click(); }");
            $browser->pause(800)->click('#btnHapusSE')->pause(2000);

            // 3. BIDANG
            echo "   -> Bidang: Tambah, Hapus...\n";
            $browser->click('a.ri-menu-item[href="/bidang"]')->pause(2000);
            $browser->script("let acc = document.querySelector('.bidang-accordion-btn'); if(acc) { acc.scrollIntoView({block:'center'}); acc.click(); }");
            $browser->pause(1500);
            $browser->script("let btn = document.querySelector('.btn-tambah-bidang'); if(btn) { btn.scrollIntoView({block:'center'}); btn.click(); }");
            $browser->pause(800)
                    ->type('#bidangNama', "Bidang $waktu")
                    ->check('#statusAktifBidang')
                    ->click('#btnSimpanBidang')->pause(2000);
            $browser->script("let btns = document.querySelectorAll('.btn-hapus-bidang'); if(btns.length) { let btn = btns[btns.length-1]; btn.scrollIntoView({block:'center'}); btn.click(); }");
            $browser->pause(800)->click('#btnHapusBidang')->pause(2000);

            // 4. USER
            echo "   -> User: Tambah, Hapus...\n";
            $browser->click('a.ri-menu-item[href="/user"]')->pause(2000);
            $browser->click('#btnTambahUser')->pause(800)
                    ->type('#inputNama', 'User Test')
                    ->type('#inputEmail', "user_$waktu@test.com")
                    ->select('#inputHakAkses', 'peserta')
                    ->type('#inputPassword', 'Password123!')
                    ->click('#btnSimpanUser')->pause(4000);
            
            $browser->script("
                let searchInput = document.querySelector('.dt-search input');
                if (searchInput) {
                    searchInput.value = 'User Test';
                    searchInput.dispatchEvent(new Event('input'));
                }
            ");
            $browser->pause(1500);

            $browser->script("let btn = document.querySelector('.btn-hapus-user'); if(btn) { btn.scrollIntoView({block:'center'}); btn.click(); }");
            $browser->pause(1500)->click('#btnHapusUser')->pause(2000);

            // 5. PENGUMUMAN
            echo "   -> Pengumuman: Tambah, Hapus...\n";
            $browser->click('a.ri-menu-item[href="/pengumuman"]')->pause(2000);
            $browser->click('#btnTambahPengumuman')->pause(800)
                    ->type('#pJudul', "Pengumuman $waktu")
                    ->type('#pDeskripsi', 'Deskripsi')
                    ->select('#pStatus', 'Draft')
                    ->click('#btnSimpanPengumuman')->pause(2000);
            $browser->script("let btns = document.querySelectorAll('.btn-hapus-pengumuman'); if(btns.length) { let btn = btns[btns.length-1]; btn.scrollIntoView({block:'center'}); btn.click(); }");
            $browser->pause(800)->click('#btnHapusPengumuman')->pause(2000);
            
            // LOGOUT
            echo "   -> Logout...\n";
            $browser->script("let avatar = document.querySelector('#kt_header_user_menu_toggle .symbol') || document.querySelector('.cursor-pointer.symbol'); if(avatar) { avatar.scrollIntoView({block:'center'}); avatar.click(); }");
            $browser->pause(1000);
            $browser->script("let signOut = Array.from(document.querySelectorAll('a')).find(a => a.textContent.includes('Sign Out')); if(signOut) { signOut.click(); }");
            $browser->pause(3000)->assertPathIs('/');

            echo "✅ MASTER TEST SELESAI\n";
        });
    }
}
