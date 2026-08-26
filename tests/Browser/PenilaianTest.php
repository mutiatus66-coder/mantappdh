<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;

class PenilaianTest extends DuskTestCase
{
    use WithTestUsers;

    protected function freshLogin(Browser $browser, $user): void
    {
        $browser->visit('/sign-in');
        $browser->driver->manage()->deleteAllCookies();
        $browser->visit('/sign-in');
        $browser->loginAs($user);
    }

    public function test_penilai_can_access_penilaian_tahap1_index(): void
    {
        $this->browse(function (Browser $browser) {
            $this->freshLogin($browser, $this->penilai());
            $browser->visit('/penilaian/tahap-1')
                    ->pause(500)
                    ->assertDontSee('Akses ditolak')
                    ->assertDontSee('Server Error');
        });
    }

    public function test_penilai_can_access_penilaian_tahap2_index(): void
    {
        $this->browse(function (Browser $browser) {
            $this->freshLogin($browser, $this->penilai());
            $browser->visit('/penilaian/tahap-2')
                    ->pause(500)
                    ->assertDontSee('Akses ditolak')
                    ->assertDontSee('Server Error');
        });
    }

    public function test_peserta_cannot_access_penilaian_tahap1(): void
    {
        $this->browse(function (Browser $browser) {
            $this->freshLogin($browser, $this->peserta());
            $browser->visit('/penilaian/tahap-1')
                    ->pause(500)
                    ->assertSee('Akses ditolak');
        });
    }
}