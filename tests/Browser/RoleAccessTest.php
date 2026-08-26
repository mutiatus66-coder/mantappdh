<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RoleAccessTest extends DuskTestCase
{
    protected function freshLogin(Browser $browser, $user): void
    {
        $browser->visit('/sign-in');
        $browser->driver->manage()->deleteAllCookies();
        $browser->visit('/sign-in');
        $browser->loginAs($user);
    }

    public function test_penilai_cannot_access_admin_page(): void
    {
        $penilai = User::where('email', 'penilai1@inovasi.test')->first();

        $this->browse(function (Browser $browser) use ($penilai) {
            $this->freshLogin($browser, $penilai);
            $browser->visit('/admin')
                    ->pause(500)
                    ->assertSee('Akses ditolak');
        });
    }

    public function test_peserta_cannot_access_admin_page(): void
    {
        $peserta = User::where('email', 'peserta1@inovasi.test')->first();

        $this->browse(function (Browser $browser) use ($peserta) {
            $this->freshLogin($browser, $peserta);
            $browser->visit('/admin')
                    ->pause(500)
                    ->assertSee('Akses ditolak');
        });
    }
}