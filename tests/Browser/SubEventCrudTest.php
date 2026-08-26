<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;

class SubEventCrudTest extends DuskTestCase
{
    use WithTestUsers;
    use InteractsWithDatabase;

    protected function freshLogin(Browser $browser, $user): void
    {
        $browser->visit('/sign-in');
        $browser->driver->manage()->deleteAllCookies();
        $browser->visit('/sign-in');
        $browser->loginAs($user);
    }

    protected function clickRowButton(Browser $browser, string $rowText, string $buttonClass, string $tbodySelector): void
    {
        $browser->script("
            const rows = document.querySelectorAll('$tbodySelector tr');
            for (const tr of rows) {
                if (tr.textContent.includes(" . json_encode($rowText) . ")) {
                    const btn = tr.querySelector('$buttonClass');
                    if (btn) btn.click();
                    break;
                }
            }
        ");
    }

    protected function createSubEvent(Browser $browser, string $namaSubEvent): void
    {
        $browser->visit('/sub-event')
                ->pause(500)
                ->click('#btnTambahSubEvent')
                ->waitFor('#seTahun', 5)
                ->type('#seTahun', '2026')
                ->type('#seSubEvent', $namaSubEvent);

        $browser->script("document.querySelector('#seMulai').value = '2026-01-01'; document.querySelector('#seMulai').dispatchEvent(new Event('change'));");
        $browser->script("document.querySelector('#seBerakhir').value = '2026-12-31'; document.querySelector('#seBerakhir').dispatchEvent(new Event('change'));");
        $browser->script("document.querySelector('#seEvent').selectedIndex = 1; document.querySelector('#seEvent').dispatchEvent(new Event('change'));");

        $browser->pause(300)
                ->click('#btnSimpanSE')
                ->waitForText('berhasil', 10)
                ->pause(500);
    }

    public function test_admin_can_create_sub_event(): void
    {
        $namaSubEvent = 'Sub Event Test ' . time();

        $this->browse(function (Browser $browser) use ($namaSubEvent) {
            $this->freshLogin($browser, $this->admin());
            $this->createSubEvent($browser, $namaSubEvent);
        });

        $this->assertDatabaseHas('sub_events', [
            'sub_event' => $namaSubEvent,
        ]);
    }

    public function test_admin_can_edit_sub_event(): void
    {
        $namaAsli = 'SE Edit Asli ' . time();
        $namaBaru = 'SE Edit Baru ' . time();

        $this->browse(function (Browser $browser) use ($namaAsli, $namaBaru) {
            $this->freshLogin($browser, $this->admin());
            $this->createSubEvent($browser, $namaAsli);

            $this->clickRowButton($browser, $namaAsli, '.btn-edit-se', '#tabelSubEventBody');
            $browser->waitFor('#seSubEvent', 5)->pause(300);

            $browser->script("document.getElementById('seSubEvent').value = '';");
            $browser->type('#seSubEvent', $namaBaru)
                    ->click('#btnSimpanSE')
                    ->waitForText('berhasil diubah', 10);
        });

        $this->assertDatabaseHas('sub_events', [
            'sub_event' => $namaBaru,
        ]);
        $this->assertDatabaseMissing('sub_events', [
            'sub_event' => $namaAsli,
        ]);
    }

    public function test_admin_can_delete_sub_event(): void
    {
        $namaSubEvent = 'SE Hapus ' . time();

        $this->browse(function (Browser $browser) use ($namaSubEvent) {
            $this->freshLogin($browser, $this->admin());
            $this->createSubEvent($browser, $namaSubEvent);

            $this->clickRowButton($browser, $namaSubEvent, '.btn-hapus-se', '#tabelSubEventBody');
            $browser->waitFor('#btnHapusSE', 5)
                    ->pause(300)
                    ->click('#btnHapusSE')
                    ->waitForText('berhasil dihapus', 10);
        });

        $this->assertDatabaseMissing('sub_events', [
            'sub_event' => $namaSubEvent,
        ]);
    }
}