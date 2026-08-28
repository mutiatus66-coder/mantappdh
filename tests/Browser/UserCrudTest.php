<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;

class UserCrudTest extends DuskTestCase
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

    protected function createUser(Browser $browser, string $namaUser, string $emailUser): void
    {
        $browser->visit('/user')
                ->pause(500)
                ->click('#btnTambahUser')
                ->waitFor('#inputNama', 5)
                ->type('#inputNama', $namaUser)
                ->type('#inputEmail', $emailUser)
                ->select('#inputHakAkses', 'peserta')
                ->type('#inputPassword', 'password123')
                ->click('#btnSimpanUser')
                ->waitForText('berhasil', 10)
                ->pause(1000);
    }

    /**
     * Cari row via kotak pencarian bawaan DataTables ("Cari:"), biar
     * tabel ke-filter jadi cuma nampilin baris yang cocok, gak peduli
     * di halaman pagination mana aslinya row itu berada.
     */
    protected function filterTableAndClick(Browser $browser, string $searchText, string $buttonClass): void
    {
        $browser->type('input[type=search]', $searchText);
        $browser->pause(800); // tunggu debounce DataTables + redraw

        $browser->driver->executeScript("
            const btn = document.querySelector('$buttonClass');
            if (btn) btn.click();
        ");
    }

    public function test_admin_can_create_user(): void
    {
        $namaUser  = 'User Test ' . time();
        $emailUser = 'usertest' . time() . '@127.0.0.1:8000';

        $this->browse(function (Browser $browser) use ($namaUser, $emailUser) {
            $this->freshLogin($browser, $this->admin());
            $this->createUser($browser, $namaUser, $emailUser);
        });

        $this->assertDatabaseHas('users', [
            'email' => $emailUser,
        ]);
    }

    // NOTE: fitur edit user tidak tersedia di UI ini (dikonfirmasi manual),
    // kemungkinan dikelola lewat panel terpisah (Filament /user/users/{id}/edit).
    // Test edit sengaja tidak dibuat di sini.

    public function test_admin_can_delete_user(): void
    {
        $namaUser  = 'User Hapus ' . time();
        $emailUser = 'userhapus' . time() . '@127.0.0.1:8000';

        $this->browse(function (Browser $browser) use ($namaUser, $emailUser) {
            $this->freshLogin($browser, $this->admin());
            $this->createUser($browser, $namaUser, $emailUser);

            $this->filterTableAndClick($browser, $emailUser, '.btn-hapus-user');

            $browser->waitFor('#btnHapusUser', 5)
                    ->pause(300)
                    ->click('#btnHapusUser')
                    ->waitForText('berhasil dihapus', 10);
        });

        $this->assertDatabaseMissing('users', [
            'email' => $emailUser,
        ]);
    }
}