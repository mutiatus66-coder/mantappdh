<?php

namespace Tests\Browser\Admin;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Facebook\WebDriver\WebDriverBy;

class Test4_PenilaianTest extends DuskTestCase
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

    public function test_penilaian_features(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080);
            echo "[4] PENILAIAN DATA\n";
            $this->loginAdmin($browser);

            // PENILAIAN TAHAP 1
            echo "   -> Penilaian Tahap 1 (Simpan Kurasi)...\n";
            $browser->click('a.ri-menu-item[href="/penilaian/tahap-1"]')->pause(2000);
            $browser->clickLink('Lihat Nilai Verifikasi')->pause(2000);
            $browser->script("let chk = document.querySelector('.chk-all'); if(chk && !chk.checked) { chk.scrollIntoView({block:'center'}); chk.click(); }");
            $browser->pause(1000);
            $browser->script("let btn = document.querySelector('.btn-rv-simpan'); if(btn) { btn.scrollIntoView({block:'center'}); btn.click(); }");
            $browser->pause(3000)->clickLink('Kembali')->pause(1500);

            // PENILAIAN TAHAP 2
            echo "   -> Penilaian Tahap 2 (Auto Ranking)...\n";
            $browser->click('a.ri-menu-item[href="/penilaian/tahap-2"]')->pause(2000);
            $browser->clickLink('Lihat Nilai Nominator')->pause(2000);
            $browser->script("let btn = document.querySelector('.btn-auto-ranking'); if(btn) { btn.scrollIntoView({block:'center'}); btn.click(); }");
            $browser->pause(2000);
            $browser->script("let btn2 = document.querySelector('.btn-simpan-ranking'); if(btn2) { btn2.scrollIntoView({block:'center'}); btn2.click(); }");
            $browser->pause(3000)->clickLink('Kembali')->pause(1500);
            
            // AUDIT LOG (RIWAYAT HALAMAN)
            echo "   -> Buka Audit Log / Riwayat Halaman...\n";
            $browser->script("if(typeof toggleHistoryPanel === 'function') toggleHistoryPanel(); else { let btn = document.querySelector('button[onclick=\"toggleHistoryPanel()\"]'); if(btn) btn.click(); }");
            $browser->pause(2000);
            $browser->assertVisible('#historyPanel');
            $browser->script("if(typeof closeHistoryPanel === 'function') closeHistoryPanel();");
            $browser->pause(1000);

            // LOGOUT
            echo "   -> Logout...\n";
            $browser->script("let avatar = document.querySelector('#kt_header_user_menu_toggle .symbol') || document.querySelector('.cursor-pointer.symbol'); if(avatar) { avatar.scrollIntoView({block:'center'}); avatar.click(); }");
            $browser->pause(1000);
            $browser->script("let signOut = Array.from(document.querySelectorAll('a')).find(a => a.textContent.includes('Sign Out')); if(signOut) { signOut.click(); }");
            $browser->pause(3000)->assertPathIs('/');

            echo "✅ PENILAIAN TEST SELESAI\n";
        });
    }
}
