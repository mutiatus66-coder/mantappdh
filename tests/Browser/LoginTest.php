<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    public function test_user_can_login(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/sign-in')
                    ->type('email', 'admin@admin.com')
                    ->type('password', 'password')
                    ->click('#kt_sign_in_submit')
                    ->pause(2800)
                    ->assertPathIs('/index');
        });
    }
} 