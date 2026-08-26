<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;

class LoginAsImpersonationTest extends DuskTestCase
{
    use WithTestUsers;

    public function test_admin_can_login_as_another_user_and_return(): void
    {
        $peserta = $this->peserta();

        $this->browse(function (Browser $browser) use ($peserta) {
            $browser->loginAs($this->admin())
                    ->visit('/user/' . $peserta->id . '/login-as')
                    ->pause(2800)
                    // TODO: sesuaikan assertion - cek elemen/teks yang menandakan
                    // sedang login sebagai peserta (misal nama peserta muncul di navbar)
                    ->assertDontSee('Forbidden')
                    ->visit('/user/login-back')
                    ->pause(2800)
                    ->assertDontSee('Forbidden');
        });
    }
}