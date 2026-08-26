<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;

class DashboardAccessTest extends DuskTestCase
{
    use WithTestUsers;

    protected function freshLogin(Browser $browser, $user): void
    {
        $browser->visit('/sign-in');
        $browser->driver->manage()->deleteAllCookies();
        $browser->visit('/sign-in');
        $browser->loginAs($user);
    }

    public function test_admin_can_access_admin_panel(): void
    {
        $this->browse(function (Browser $browser) {
            $this->freshLogin($browser, $this->admin());
            $browser->visit('/admin')
                    ->pause(500)
                    ->assertDontSee('Akses ditolak');
        });
    }

    public function test_penilai_cannot_access_admin_panel(): void
    {
        $this->browse(function (Browser $browser) {
            $this->freshLogin($browser, $this->penilai());
            $browser->visit('/admin')
                    ->pause(500)
                    ->assertSee('Akses ditolak');
        });
    }

    public function test_peserta_cannot_access_admin_panel(): void
    {
        $this->browse(function (Browser $browser) {
            $this->freshLogin($browser, $this->peserta());
            $browser->visit('/admin')
                    ->pause(500)
                    ->assertSee('Akses ditolak');
        });
    }

    public function test_admin_can_access_kelola_penilai(): void
    {
        $this->browse(function (Browser $browser) {
            $this->freshLogin($browser, $this->admin());
            $browser->visit('/penilai')
                    ->pause(500)
                    ->assertDontSee('Akses ditolak');
        });
    }

    public function test_penilai_cannot_access_kelola_penilai(): void
    {
        $this->browse(function (Browser $browser) {
            $this->freshLogin($browser, $this->penilai());
            $browser->visit('/penilai')
                    ->pause(500)
                    ->assertSee('Akses ditolak');
        });
    }

    public function test_peserta_can_access_riwayat_usulan(): void
    {
        $this->browse(function (Browser $browser) {
            $this->freshLogin($browser, $this->peserta());
            $browser->visit('/inovasi/riwayat')
                    ->pause(500)
                    ->assertDontSee('Akses ditolak');
        });
    }
}