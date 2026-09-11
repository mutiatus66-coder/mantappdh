<?php

namespace Tests\Browser\Admin;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Facebook\WebDriver\WebDriverBy;

class Test3_InovasiTest extends DuskTestCase
{
    private function loginAdmin(Browser $browser): void
    {
        $browser->visit('http://mantappdh.test/')
                ->pause(1000)
                ->clickLink('Login')
                ->pause(2000)
                ->type('email', 'admin@admin.com')
                ->type('password', 'password')
                ->click('button[type="submit"]')
                ->pause(3000)
                ->assertPathIs('/index');
    }

    public function test_inovasi_features(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080);
            echo "[3] INOVASI DATA\n";
            $this->loginAdmin($browser);

            // RIWAYAT INOVASI
            echo "   -> Riwayat Inovasi...\n";
            $browser->click('a.ri-menu-item[href="/inovasi/riwayat"]')->pause(2000);
            $browser->clickLink('Lihat Usulan')->pause(2000);
            $browser->script("let input = document.querySelector('.dt-search input'); if(input) { input.value = 'a'; input.dispatchEvent(new Event('input')); }");
            $browser->pause(1000);
            $browser->clickLink('Kembali')->pause(1500);

            // REKAP NILAI
            echo "   -> Rekap Nilai (Export)...\n";
            $browser->click('a.ri-menu-item[href="/inovasi/rekap-nilai"]')->pause(2000);
            $browser->clickLink('Lihat Nilai')->pause(3000);
            $browser->script("let btnPdf = document.querySelector('.buttons-pdf'); if(btnPdf) { btnPdf.scrollIntoView({block:'center'}); btnPdf.click(); }");
            $browser->pause(2000);
            $browser->script("let btnExcel = document.querySelector('.buttons-excel'); if(btnExcel) { btnExcel.scrollIntoView({block:'center'}); btnExcel.click(); }");
            $browser->pause(2000);
            $browser->clickLink('Kembali')->pause(1500);
            
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

            echo "✅ INOVASI TEST SELESAI\n";
        });
    }
}
