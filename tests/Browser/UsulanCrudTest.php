<?php

namespace Tests\Browser;

use App\Models\SubEvent;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;

class UsulanCrudTest extends DuskTestCase
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

    public function test_peserta_can_create_usulan(): void
    {
        $namaInovasi = 'Inovasi Test ' . time();
        $judul       = 'Judul Test ' . time();

        $this->browse(function (Browser $browser) use ($namaInovasi, $judul) {
            $this->freshLogin($browser, $this->peserta());

            $subEventId = SubEvent::first()->id;

            $browser->visit('/inovasi/usulan/' . $subEventId)
                    ->pause(500)
                    ->click('#btnTambah')
                    ->waitFor('#fNamaInovasi', 5);

            // ── LANGKAH 1: Data Tim ──
            $browser->type('#fNamaInovasi', $namaInovasi)
                    ->type('#fJudul', $judul);

            $browser->script("document.querySelector('#fBidangId').selectedIndex = 1; document.querySelector('#fBidangId').dispatchEvent(new Event('change'));");

            $browser->type('#fInteraksi', 'Teknologi Tepat Guna')
                    ->select('#fKategori', 'umum')
                    ->type('#fInovator', 'Instansi Test')
                    ->type('#fKetuaNama', 'Ketua Test')
                    ->type('#fKetuaEmail', 'ketuatest' . time() . '@test.com')
                    ->type('#fKetuaWa', '081234567890')
                    ->type('#fAlamatKetua', 'Alamat Test')
                    ->type('#fKtp', '1234567890123456');

                // ── LANGKAH 1 → LANGKAH 2 ──
            $browser->waitFor('#btnNext', 5)
                    ->click('#btnNext')
                    ->pause(1000);

                // ── LANGKAH 2 → LANGKAH 3 ──
            $browser->waitFor('#btnNext', 5)
                    ->click('#btnNext')
                    ->pause(1500);

                // ── LANGKAH 3 → SUBMIT ──
            $browser->waitFor('#btnSimpan', 10)
                    ->scrollIntoView('#btnSimpan')
                    ->click('#btnSimpan')
                    ->waitForText('berhasil', 10);
            });

        $this->assertDatabaseHas('usulans', [
            'nama_inovasi' => $namaInovasi,
            'judul' => $judul,
        ]);
    }
}