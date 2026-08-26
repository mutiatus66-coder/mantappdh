<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;

class PengumumanCrudTest extends DuskTestCase
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

    protected function filterTableAndClick(Browser $browser, string $searchText, string $buttonClass): void
    {
        $browser->type('input[type=search]', $searchText);
        $browser->pause(800);

        $browser->driver->executeScript("
            const btn = document.querySelector('$buttonClass');
            if (btn) btn.click();
        ");
    }

    protected function createPengumuman(Browser $browser, string $judul): void
    {
        $browser->visit('/pengumuman')
                ->pause(500)
                ->click('#btnTambahPengumuman')
                ->waitFor('#pJudul', 5)
                ->type('#pJudul', $judul)
                ->type('#pDeskripsi', 'Deskripsi test otomatis dari Dusk')
                ->select('#pStatus', 'Published')
                ->click('#btnSimpanPengumuman')
                ->waitForText('berhasil', 10)
                ->pause(1000);
    }

    public function test_admin_can_create_pengumuman(): void
    {
        $judul = 'Pengumuman Test ' . time();

        $this->browse(function (Browser $browser) use ($judul) {
            $this->freshLogin($browser, $this->admin());
            $this->createPengumuman($browser, $judul);
        });

        $this->assertDatabaseHas('pengumuman', [
            'judul' => $judul,
        ]);
    }

    public function test_admin_can_edit_pengumuman(): void
    {
        $judulAsli = 'Pengumuman Edit Asli ' . time();
        $judulBaru = 'Pengumuman Edit Baru ' . time();

        $this->browse(function (Browser $browser) use ($judulAsli, $judulBaru) {
            $this->freshLogin($browser, $this->admin());
            $this->createPengumuman($browser, $judulAsli);

            $this->filterTableAndClick($browser, $judulAsli, '.btn-edit-pengumuman');
            $browser->waitFor('#pJudul', 5)->pause(300);

            $browser->script("document.getElementById('pJudul').value = '';");
            $browser->type('#pJudul', $judulBaru)
                    ->click('#btnSimpanPengumuman')
                    ->waitForText('berhasil diubah', 10);
        });

        $this->assertDatabaseHas('pengumuman', [
            'judul' => $judulBaru,
        ]);
        $this->assertDatabaseMissing('pengumuman', [
            'judul' => $judulAsli,
        ]);
    }

    public function test_admin_can_delete_pengumuman(): void
    {
        $judul = 'Pengumuman Hapus ' . time();

        $this->browse(function (Browser $browser) use ($judul) {
            $this->freshLogin($browser, $this->admin());
            $this->createPengumuman($browser, $judul);

            $this->filterTableAndClick($browser, $judul, '.btn-hapus-pengumuman');
            $browser->waitFor('#btnHapusPengumuman', 5)
                    ->pause(300)
                    ->click('#btnHapusPengumuman')
                    ->waitForText('berhasil dihapus', 10);
        });

        $this->assertDatabaseMissing('pengumuman', [
            'judul' => $judul,
        ]);
    }
}