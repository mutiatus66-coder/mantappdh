<?php

namespace Tests\Browser;

use App\Models\SubEvent;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;


class UsulanEditDeleteTest extends DuskTestCase
{
    use WithTestUsers;
    use InteractsWithDatabase;
    protected function freshLogin(Browser $browser, $user): Void
    {
        $browser->visit('/sign-in');
        $browser->driver->manage()->deleteAllCookies();
        $browser->visit('/sign-in');
        $browser->loginAs($user);
    }
   public function test_peserta_can_edit_usulan(): Void
{
    $namaBaru = 'Inovasi Sudah Diedit ' . time();

    $this->browse(function (Browser $browser) use ($namaBaru) {
            $this->freshLogin($browser, $this->peserta());

            $subEventId = SubEvent::first()->id;
            $browser->visit('/inovasi/usulan/' . $subEventId)
                    ->pause(500)
                    ->click('.btn-edit')
                    ->waitFor('#fNamaInovasi', 5);
            $namaLama = $browser->driver->executeScript("return document.getElementById('fNamaInovasi').value;");
                    fwrite(STDOUT, "\n[Nama Inovasi yang lagi di-edit: $namaLama]\n\n");
            $browser->script("document.getElementById('fNamaInovasi').value = '';");
            $browser->type('#fNamaInovasi', $namaBaru);

            $browser->click('#btnNext')->pause(500);
            $browser->click('#btnNext')->pause(500);
            $browser->waitForReload(function ($browser) {
            $browser->click('#btnSimpan');
});
            $isiHalaman = $browser->driver->executeScript("return document.body.innerText;");
            $logs = $browser->driver->manage()->getLog('browser');
                fwrite(STDOUT, "\n--- CONSOLE LOG ---\n");
                foreach ($logs as $log) {
                fwrite(STDOUT, $log['level'] . ": " . $log['message'] . "\n");
                }
                fwrite(STDOUT, "--- END ---\n\n");
                fwrite(STDOUT, "\n[Apakah nama baru muncul di halaman?: " . (str_contains($isiHalaman, $namaBaru) ? 'YA' : 'TIDAK') . "]\n\n");
                });

        $this->assertDatabaseHas('usulans', [
            'nama_inovasi' => $namaBaru,
        ]);
    }
}
