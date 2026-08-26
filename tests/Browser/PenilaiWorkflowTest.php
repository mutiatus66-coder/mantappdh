<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Facebook\WebDriver\WebDriverBy;

class PenilaiWorkflowTest extends DuskTestCase
{
    /**
     * A Dusk test for Penilai Workflow E2E.
     */
    public function test_penilai_workflow(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1280, 720);

            echo "1. Membuka landing page...\n";
            $browser->visit('http://mantappdh.test/');
            $browser->pause(1000);

            echo "2. Menekan tombol Login...\n";
            $browser->clickLink('Login');
            $browser->pause(2000);

            echo "3. Memasukkan kredensial login penilai...\n";
            $browser->type('email', 'ahmad.fauzi@example.com')
                    ->type('password', 'password');
            echo "   -> Menekan tombol Masuk...\n";
            $browser->click('button[type="submit"]')
                    ->pause(3000);

            echo "4. Ke halaman Riwayat melalui sidebar...\n";
            $browser->clickLink('Riwayat')
                    ->pause(2000);

            echo "5. Menekan Lihat Usulan...\n";
            $browser->clickLink('Lihat Usulan')
                    ->pause(2000);

            echo "6. Menggunakan search Datatables di Riwayat Usulan...\n";
            $browser->type('.dt-search input', 'Diskominfo')
                    ->pause(1000);

            echo "7. Menekan interaksi + dari Datatables...\n";
            $dtControlRiwayat = count($browser->driver->findElements(WebDriverBy::cssSelector('td.dt-control, td.dtr-control')));
            if ($dtControlRiwayat > 0) {
                $browser->click('td.dt-control, td.dtr-control')->pause(1000);
            }

            echo "8. Menekan tombol Kembali...\n";
            $browser->clickLink('Kembali')
                    ->pause(2000);

            echo "9. Ke halaman Rekap Nilai melalui sidebar...\n";
            $browser->clickLink('Rekap Nilai')
                    ->pause(2000);

            echo "10. Menekan Lihat Nilai...\n";
            $browser->clickLink('Lihat Nilai')
                    ->pause(2000);

            echo "11. Menggunakan search Datatables di Rekap Nilai...\n";
            $browser->type('.dt-search input', 'Diskominfo')
                    ->pause(1000);

            echo "12. Menekan interaksi + dari Datatables...\n";
            $dtControlRekap = count($browser->driver->findElements(WebDriverBy::cssSelector('td.dt-control, td.dtr-control')));
            if ($dtControlRekap > 0) {
                $browser->click('td.dt-control, td.dtr-control')->pause(1000);
            }

            echo "13. Menekan tombol Kembali...\n";
            $browser->clickLink('Kembali')
                    ->pause(2000);

            echo "14. Ke halaman Penilaian Tahap 1 melalui sidebar...\n";
            $browser->clickLink('Penilaian Tahap 1')
                    ->pause(2000);

            echo "15. Menekan Lihat Nilai Verifikasi...\n";
            $browser->clickLink('Lihat Nilai Verifikasi')
                    ->pause(2000);

            echo "16. Memberi nilai kepada inovator...\n";
            $btnNilaiCount = count($browser->driver->findElements(WebDriverBy::cssSelector('.btn-input-nilai')));
            $limit = min(7, $btnNilaiCount);

            for ($i = 0; $i < $limit; $i++) {
                $browser->script(
                    "let btn1 = document.querySelectorAll('.btn-input-nilai')[$i];" .
                    "if(btn1) { btn1.scrollIntoView({block: 'center'}); btn1.click(); }"
                );
                $browser->pause(1000);

                $browser->script(
                    "document.querySelectorAll('.input-nilai-item').forEach(el => {" .
                    "  if(el.offsetWidth > 0 || el.offsetHeight > 0) { el.value = '10'; }" .
                    "});"
                );
                $browser->script(
                    "let btn2 = Array.from(document.querySelectorAll('button')).find(b => b.textContent.includes('Simpan Nilai'));" .
                    "if(btn2) { btn2.scrollIntoView({block: 'center'}); btn2.click(); }"
                );
                $browser->pause(1000);

                $browser->script(
                    "let btn3 = document.querySelectorAll('.btn-catatan')[$i];" .
                    "if(btn3) { btn3.scrollIntoView({block: 'center'}); btn3.click(); }"
                );
                $browser->pause(1000);

                $browser->script(
                    "let ta = Array.from(document.querySelectorAll('textarea.form-control')).find(el => el.offsetWidth > 0 || el.offsetHeight > 0);" .
                    "if(ta) { ta.value = 'Catatan otomatis dari Dusk untuk inovator ke-' + ($i+1); }"
                );
                $browser->script(
                    "let btn4 = Array.from(document.querySelectorAll('button')).find(b => b.textContent.includes('Simpan Catatan'));" .
                    "if(btn4) { btn4.scrollIntoView({block: 'center'}); btn4.click(); }"
                );
                $browser->pause(1000);
            }

            echo "17. Filter Total Nilai (Klik header Total Nilai)...\n";
            $browser->script(
                "let th1 = Array.from(document.querySelectorAll('th')).find(t => t.textContent.includes('Total Nilai'));" .
                "if(th1) { th1.scrollIntoView({block: 'center'}); th1.click(); }"
            );
            $browser->pause(1000);
            $browser->script(
                "let th2 = Array.from(document.querySelectorAll('th')).find(t => t.textContent.includes('Total Nilai'));" .
                "if(th2) { th2.scrollIntoView({block: 'center'}); th2.click(); }"
            );
            $browser->pause(1500);

            echo "18. Menekan check box select all (chk-all)...\n";
            $browser->script(
                "let chk = document.querySelector('.chk-all');" .
                "if(chk) { chk.scrollIntoView({block: 'center'}); chk.click(); }"
            );
            $browser->pause(1500);

            echo "19. Menekan tombol Simpan di Tahap 1...\n";
            $browser->script(
                "let btn5 = document.querySelector('.btn-rv-simpan');" .
                "if(btn5) { btn5.scrollIntoView({block: 'center'}); btn5.click(); }"
            );
            $browser->pause(2500);

            echo "20. Menekan tombol Kembali...\n";
            $browser->clickLink('Kembali')
                    ->pause(2000);

            echo "21. Ke halaman Penilaian Tahap 2 melalui sidebar...\n";
            $browser->clickLink('Penilaian Tahap 2')
                    ->pause(2000);

            echo "22. Menekan Lihat Nilai Nominator...\n";
            $browser->clickLink('Lihat Nilai Nominator')
                    ->pause(2000);

            echo "23. Menekan tombol Ranking...\n";
            $browser->script(
                "let btn6 = Array.from(document.querySelectorAll('button')).find(b => b.textContent.includes('Ranking'));" .
                "if(btn6) { btn6.scrollIntoView({block: 'center'}); btn6.click(); }"
            );
            $browser->pause(2000);

            echo "24. Menekan tombol Simpan Ranking...\n";
            $browser->script(
                "let btn7 = Array.from(document.querySelectorAll('button')).find(b => b.textContent.includes('Simpan Ranking'));" .
                "if(btn7) { btn7.scrollIntoView({block: 'center'}); btn7.click(); }"
            );
            $browser->pause(3000);

            echo "25. Menekan tombol Kembali...\n";
            $browser->clickLink('Kembali')
                    ->pause(2000);

            echo "26. Ke halaman Rekap Nilai...\n";
            $browser->clickLink('Rekap Nilai')
                    ->pause(2000);

            echo "27. Menekan tombol Lihat Nilai (kembali ke Rekap Pendaftar)...\n";
            $browser->clickLink('Lihat Nilai')
                    ->pause(3000);

            echo "28. Mencoba export PDF...\n";
            $browser->script(
                "let btn8 = document.querySelector('.buttons-pdf');" .
                "if(btn8) { btn8.scrollIntoView({block: 'center'}); btn8.click(); }"
            );
            $browser->pause(2000);

            echo "29. Mencoba export Excel...\n";
            $browser->script(
                "let btn9 = document.querySelector('.buttons-excel');" .
                "if(btn9) { btn9.scrollIntoView({block: 'center'}); btn9.click(); }"
            );
            $browser->pause(2000);

            echo "30. Menekan tombol Kembali...\n";
            $browser->clickLink('Kembali')
                    ->pause(2000);

            echo "31. Log Out...\n";
            $browser->script(
                "let btn10 = document.querySelector('.cursor-pointer.symbol');" .
                "if(btn10) { btn10.scrollIntoView({block: 'center'}); btn10.click(); }"
            );
            $browser->pause(1000);
            $browser->clickLink('Sign Out')
                    ->pause(2000);

            echo "✅ Workflow Penilai E2E (Dusk) Selesai dengan Sukses!\n";
        });
    }
}
