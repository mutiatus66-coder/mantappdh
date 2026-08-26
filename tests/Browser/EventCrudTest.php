<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;

class EventCrudTest extends DuskTestCase
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

    protected function createEvent(Browser $browser, string $namaEvent): void
    {
        $browser->visit('/event')
                ->pause(500)
                ->click('#btnTambahEvent')
                ->waitFor('#inputNamaEvent', 5)
                ->type('#inputNamaEvent', $namaEvent)
                ->select('#inputJenis', 'INOTEK')
                ->click('#btnSimpanEvent')
                ->waitForText('berhasil', 10)
                ->pause(500);
    }

    /**
     * Cari baris tabel yang mengandung teks tertentu, lalu klik tombol
     * dengan class tertentu di dalam baris itu. Robust terhadap posisi
     * halaman/pagination karena mencari langsung di seluruh DOM.
     */
    protected function clickRowButton(
    Browser $browser,
    string $rowText,
    string $buttonClass,
    string $tbodySelector
    ): void {
    // Cari data melalui search DataTables
    $browser->type('input[type="search"]', $rowText)
            ->pause(800);

    // Setelah hasil search muncul, klik tombol di baris yang sesuai
    $browser->script("
        const rows = document.querySelectorAll('$tbodySelector tr');

        for (const tr of rows) {
            if (tr.textContent.includes(" . json_encode($rowText) . ")) {
                const btn = tr.querySelector('$buttonClass');

                if (btn) {
                    btn.click();
                }

                break;
            }
        }
    ");

    $browser->pause(500);
        }

    public function test_admin_can_create_event(): void
    {
        $namaEvent = 'Event Test ' . time();

        $this->browse(function (Browser $browser) use ($namaEvent) {
            $this->freshLogin($browser, $this->admin());
            $this->createEvent($browser, $namaEvent);
        });

        $this->assertDatabaseHas('events', [
            'nama_event' => $namaEvent,
        ]);
    }

    public function test_admin_can_edit_event(): void
    {
        $namaAsli = 'Event Edit Asli ' . time();
        $namaBaru = 'Event Edit Baru ' . time();

        $this->browse(function (Browser $browser) use ($namaAsli, $namaBaru) {
        $this->freshLogin($browser, $this->admin());
        $this->createEvent($browser, $namaAsli);

        $this->clickRowButton($browser,$namaAsli,'.btn-edit-event','#tabelEventBody');
        $browser->waitFor('#inputNamaEvent', 5)
                ->pause(300);

            // Kosongkan field nama, isi dengan nama baru
        $browser->script("document.getElementById('inputNamaEvent').value = '';");
        $browser->type('#inputNamaEvent', $namaBaru)
                    ->click('#btnSimpanEvent')
                    ->waitForText('berhasil diubah', 10);
        });

        $this->assertDatabaseHas('events', [
            'nama_event' => $namaBaru,
        ]);
        $this->assertDatabaseMissing('events', [
            'nama_event' => $namaAsli,
        ]);
    }

    public function test_admin_can_delete_event(): void
    {    
        $namaEvent = 'Event Hapus ' . time();

        $this->browse(function (Browser $browser) use ($namaEvent) {
        $this->freshLogin($browser, $this->admin());
        $this->createEvent($browser, $namaEvent);

        $this->clickRowButton($browser, $namaEvent, '.btn-hapus-event', '#tabelEventBody');
        $browser->waitFor('#btnHapusEvent', 5)
                ->pause(300)
                ->click('#btnHapusEvent')
                ->waitForText('berhasil dihapus', 10);
        });

        $this->assertDatabaseMissing('events', [
            'nama_event' => $namaEvent,
        ]);
    }
}