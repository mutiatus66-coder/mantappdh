<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;

class BidangCrudTest extends DuskTestCase
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

    protected function createBidang(Browser $browser, string $namaBidang): void
    {
        $browser->visit('/bidang')
                ->pause(500)
                ->click('.bidang-accordion-btn')
                ->pause(600)
                ->click('.btn-tambah-bidang')
                ->waitFor('#bidangNama', 5)
                ->type('#bidangNama', $namaBidang)
                ->click('#btnSimpanBidang')
                ->waitForText('berhasil', 10)
                ->pause(500);
    }

    public function test_admin_can_create_bidang(): void
    {
        $namaBidang = 'Bidang Test ' . time();

        $this->browse(function (Browser $browser) use ($namaBidang) {
            $this->freshLogin($browser, $this->admin());
            $this->createBidang($browser, $namaBidang);
        });

        $this->assertDatabaseHas('bidangs', [
            'nama' => $namaBidang,
        ]);
    }

    public function test_admin_can_edit_bidang(): void
    {
        $namaAsli = 'Bidang Edit Asli ' . time();
        $namaBaru = 'Bidang Edit Baru ' . time();

        $this->browse(function (Browser $browser) use ($namaAsli, $namaBaru) {
            $this->freshLogin($browser, $this->admin());
            $this->createBidang($browser, $namaAsli);

            // Tabel bidang ada per sub-event (id tbody dinamis: tbody-se-{id}),
            // cari tombol edit di seluruh tabel yang sedang terbuka (expanded)
            $this->clickRowButton($browser, $namaAsli, '.btn-ubah-bidang', 'tbody[id^="tbody-se-"]');
            $browser->waitFor('#bidangNama', 5)->pause(300);

            $browser->script("document.getElementById('bidangNama').value = '';");
            $browser->type('#bidangNama', $namaBaru)
                    ->click('#btnSimpanBidang')
                    ->waitForText('berhasil diubah', 10);
        });

        $this->assertDatabaseHas('bidangs', [
            'nama' => $namaBaru,
        ]);
        $this->assertDatabaseMissing('bidangs', [
            'nama' => $namaAsli,
        ]);
    }

    public function test_admin_can_delete_bidang(): void
    {
        $namaBidang = 'Bidang Hapus ' . time();

        $this->browse(function (Browser $browser) use ($namaBidang) {
            $this->freshLogin($browser, $this->admin());
            $this->createBidang($browser, $namaBidang);

            $this->clickRowButton($browser, $namaBidang, '.btn-hapus-bidang', 'tbody[id^="tbody-se-"]');
            $browser->waitFor('#btnHapusBidang', 5)
                    ->pause(300)
                    ->click('#btnHapusBidang')
                    ->waitForText('berhasil dihapus', 10);
        });

        $this->assertDatabaseMissing('bidangs', [
            'nama' => $namaBidang,
        ]);
    }
}