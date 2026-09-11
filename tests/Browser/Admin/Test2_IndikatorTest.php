<?php

namespace Tests\Browser\Admin;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Facebook\WebDriver\WebDriverBy;

class Test2_IndikatorTest extends DuskTestCase
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

    public function test_indikator_features(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080);
            echo "[2] INDIKATOR DATA\n";
            $this->loginAdmin($browser);

            // INDIKATOR TAHAP 1
            echo "   -> Indikator Tahap 1...\n";
            $browser->click('a.ri-menu-item[href="/indikator/tahap-1"]')->pause(2000);
            $browser->script("let btn = document.querySelector('.btn-open-formulasi1'); if(btn) { btn.scrollIntoView({block:'center'}); btn.click(); }");
            $browser->pause(800);
            $formulasi1Count = count($browser->driver->findElements(WebDriverBy::cssSelector('#modalFormulasi1')));
            if ($formulasi1Count > 0) {
                $browser->script("document.getElementById('inputNilaiMakalah').value = '40'; document.getElementById('inputNilaiMakalah').dispatchEvent(new Event('input'));");
                $browser->script("document.getElementById('inputNilaiSubstansi').value = '60'; document.getElementById('inputNilaiSubstansi').dispatchEvent(new Event('input'));");
                $browser->pause(500);
                $browser->script("let btn = document.getElementById('btnSimpan1'); if(btn && !btn.disabled) btn.click();");
                $browser->pause(1500);
            }
            
            // INDIKATOR TAHAP 2
            echo "   -> Indikator Tahap 2...\n";
            $browser->click('a.ri-menu-item[href="/indikator/tahap-2"]')->pause(2000);
            $browser->script("let btn2 = document.querySelector('.btn-open-formulasi'); if(btn2) { btn2.scrollIntoView({block:'center'}); btn2.click(); }");
            $browser->pause(800);
            $formulasi2Count = count($browser->driver->findElements(WebDriverBy::cssSelector('#modalFormulasi')));
            if ($formulasi2Count > 0) {
                $browser->script("document.getElementById('inputNilaiInovasi').value = '50'; document.getElementById('inputNilaiInovasi').dispatchEvent(new Event('input'));");
                $browser->script("document.getElementById('inputNilaiPeragaan').value = '50'; document.getElementById('inputNilaiPeragaan').dispatchEvent(new Event('input'));");
                $browser->pause(500);
                $browser->script("let btn = document.getElementById('btnSimpan2'); if(btn && !btn.disabled) btn.click();");
                $browser->pause(1500);
            }
            
            // LOGOUT
            echo "   -> Logout...\n";
            $browser->script("let avatar = document.querySelector('#kt_header_user_menu_toggle .symbol') || document.querySelector('.cursor-pointer.symbol'); if(avatar) { avatar.scrollIntoView({block:'center'}); avatar.click(); }");
            $browser->pause(1000);
            $browser->script("let signOut = Array.from(document.querySelectorAll('a')).find(a => a.textContent.includes('Sign Out')); if(signOut) { signOut.click(); }");
            $browser->pause(3000)->assertPathIs('/');

            echo "✅ INDIKATOR TEST SELESAI\n";
        });
    }
}
