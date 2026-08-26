<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;

class AdminCrudPagesTest extends DuskTestCase
{
    use WithTestUsers;

    public function test_admin_can_view_event_index(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                    ->visit('/event')
                    ->assertDontSee('Forbidden')
                    ->assertDontSee('Server Error');
        });
    }

    public function test_admin_can_view_sub_event_index(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                    ->visit('/sub-event')
                    ->assertDontSee('Forbidden')
                    ->assertDontSee('Server Error');
        });
    }

    public function test_admin_can_view_bidang_index(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                    ->visit('/bidang')
                    ->assertDontSee('Forbidden')
                    ->assertDontSee('Server Error');
        });
    }

    public function test_admin_can_view_user_index(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                    ->visit('/user')
                    ->assertDontSee('Forbidden')
                    ->assertDontSee('Server Error');
        });
    }
}
