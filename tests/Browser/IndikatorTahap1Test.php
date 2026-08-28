<?php

namespace Tests\Browser;

use App\Models\Indikator;
use App\Models\SubEvent;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;

class IndikatorTahap1Test extends DuskTestCase
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

    /**
     * Mencari tombol pada baris tabel berdasarkan teks indikator.
     */
    protected function clickRowButton(
        Browser $browser,
        string $rowText,
        string $buttonClass,
        string $tbodySelector
    ): void {
        $browser->script("
            const rows = document.querySelectorAll('$tbodySelector tr');

            for (const tr of rows) {
                if (tr.textContent.includes(" . json_encode($rowText) . ")) {
                    const btn = tr.querySelector('$buttonClass');

                    if (btn) {
                        btn.click();
                    }

                    break;
                }
            }
        ");
    }

    /**
     * Membuat indikator melalui UI.
     */
   protected function createIndikator(
    Browser $browser,
    string $namaIndikator
): void {
    $subEventId = 1;

    $browser->visit("/indikator/tahap-1/{$subEventId}/inovasi")
            ->waitFor('#btnTambahIndikator', 5)
            ->click('#btnTambahIndikator')
            ->waitFor('#inputNamaIndikator', 5)
            ->type('#inputNamaIndikator', $namaIndikator)
            ->select('#selectJenis', 'substansi')
            ->click('#btnSimpanIndikator')
            ->waitForText('berhasil ditambahkan', 10)
            ->pause(500);
}

    /**
     * Mengambil indikator yang baru dibuat berdasarkan nama.
     */
    protected function getIndikator(string $namaIndikator): Indikator
    {
        return Indikator::where('nama_indikator', $namaIndikator)
            ->latest('id')
            ->firstOrFail();
    }

    public function test_admin_can_create_indikator_tahap1(): void
    {
        $namaIndikator = 'Indikator Test ' . time();

        $this->browse(function (Browser $browser) use ($namaIndikator) {
            $this->freshLogin($browser, $this->admin());

            $this->createIndikator($browser, $namaIndikator);
        });

        $this->assertDatabaseHas('indikators', [
            'nama_indikator' => $namaIndikator,
            'jenis' => 'substansi',
        ]);
    }

    public function test_admin_can_edit_indikator_tahap1(): void
    {
        $namaLama = 'Indikator Edit Lama ' . time();
        $namaBaru = 'Indikator Edit Baru ' . time();

        $this->browse(function (Browser $browser) use ($namaLama, $namaBaru) {
            $this->freshLogin($browser, $this->admin());

            $this->createIndikator($browser, $namaLama);

            $this->clickRowButton(
                $browser,
                $namaLama,
                '.btn-edit-indikator',
                '#tabelIndikatorBody'
            );

            $browser->waitFor('#inputNamaIndikator', 5)
                    ->pause(300);

            $browser->script("
                document.getElementById('inputNamaIndikator').value = '';
            ");

            $browser->type('#inputNamaIndikator', $namaBaru)
                    ->select('#selectJenis', 'makalah')
                    ->click('#btnSimpanIndikator')
                    ->waitForText('berhasil diperbarui', 10);
        });

        $this->assertDatabaseHas('indikators', [
            'nama_indikator' => $namaBaru,
            'jenis' => 'makalah',
        ]);

        $this->assertDatabaseMissing('indikators', [
            'nama_indikator' => $namaLama,
        ]);
    }

    public function test_admin_can_add_keterangan_indikator_tahap1(): void
    {
        $namaIndikator = 'Indikator Keterangan ' . time();

        $this->browse(function (Browser $browser) use ($namaIndikator) {
            $this->freshLogin($browser, $this->admin());

            $this->createIndikator($browser, $namaIndikator);

            $indikator = $this->getIndikator($namaIndikator);

            $browser->visit(
                "/indikator/tahap-1/{$indikator->sub_event_id}/detail/{$indikator->id}"
            );

            $browser->pause(500)
                    ->click('#btnTambahKeterangan')
                    ->waitFor('#inputKeterangan', 5)
                    ->type(
                        '#inputKeterangan',
                        'Keterangan test indikator'
                    )
                    ->type('#inputNilaiMinimal', '10')
                    ->type('#inputNilaiMaksimal', '25')
                    ->click('#btnSimpanKeterangan')
                    ->waitForText('berhasil ditambahkan', 10);
        });

        $this->assertDatabaseHas('keterangan_indikators', [
            'indikator_id' => $this->getIndikator($namaIndikator)->id,
            'keterangan' => 'Keterangan test indikator',
            'nilai_minimal' => 10,
            'nilai_maksimal' => 25,
        ]);
    }

    public function test_admin_can_edit_keterangan_indikator_tahap1(): void
    {
        $namaIndikator = 'Indikator Edit Keterangan ' . time();

        $this->browse(function (Browser $browser) use ($namaIndikator) {
            $this->freshLogin($browser, $this->admin());

            $this->createIndikator($browser, $namaIndikator);

            $indikator = $this->getIndikator($namaIndikator);

            $browser->visit(
                "/indikator/tahap-1/{$indikator->sub_event_id}/detail/{$indikator->id}"
            );

            $browser->pause(500)
                    ->click('#btnTambahKeterangan')
                    ->waitFor('#inputKeterangan', 5)
                    ->type(
                        '#inputKeterangan',
                        'Keterangan Lama'
                    )
                    ->type('#inputNilaiMinimal', '10')
                    ->type('#inputNilaiMaksimal', '25')
                    ->click('#btnSimpanKeterangan')
                    ->waitForText('berhasil ditambahkan', 10);

            $this->clickRowButton(
                $browser,
                'Keterangan Lama',
                '.btn-edit-keterangan',
                '#tabelKeteranganBody'
            );

            $browser->waitFor('#inputKeterangan', 5)
                    ->pause(300);

            $browser->script("
                document.getElementById('inputKeterangan').value = '';
                document.getElementById('inputNilaiMinimal').value = '';
                document.getElementById('inputNilaiMaksimal').value = '';
            ");

            $browser->type('#inputKeterangan', 'Keterangan Baru')
                    ->type('#inputNilaiMinimal', '20')
                    ->type('#inputNilaiMaksimal', '40')
                    ->click('#btnSimpanKeterangan')
                    ->waitForText('berhasil diperbarui', 10);
        });

        $indikator = $this->getIndikator($namaIndikator);

        $this->assertDatabaseHas('keterangan_indikators', [
            'indikator_id' => $indikator->id,
            'keterangan' => 'Keterangan Baru',
            'nilai_minimal' => 20,
            'nilai_maksimal' => 40,
        ]);

        $this->assertDatabaseMissing('keterangan_indikators', [
            'indikator_id' => $indikator->id,
            'keterangan' => 'Keterangan Lama',
        ]);
    }

    public function test_admin_can_delete_keterangan_indikator_tahap1(): void
    {
        $namaIndikator = 'Indikator Hapus Keterangan ' . time();

        $this->browse(function (Browser $browser) use ($namaIndikator) {
            $this->freshLogin($browser, $this->admin());

            $this->createIndikator($browser, $namaIndikator);

            $indikator = $this->getIndikator($namaIndikator);

            $browser->visit(
                "/indikator/tahap-1/{$indikator->sub_event_id}/detail/{$indikator->id}"
            );

            $browser->pause(500)
                    ->click('#btnTambahKeterangan')
                    ->waitFor('#inputKeterangan', 5)
                    ->type(
                        '#inputKeterangan',
                        'Keterangan Untuk Dihapus'
                    )
                    ->type('#inputNilaiMinimal', '10')
                    ->type('#inputNilaiMaksimal', '25')
                    ->click('#btnSimpanKeterangan')
                    ->waitForText('berhasil ditambahkan', 10);

            $this->clickRowButton(
                $browser,
                'Keterangan Untuk Dihapus',
                '.btn-hapus-keterangan',
                '#tabelKeteranganBody'
            );

            $browser->waitFor('#btnHapusKeterangan', 5)
                    ->pause(300)
                    ->click('#btnHapusKeterangan')
                    ->waitForText('berhasil dihapus', 10);
        });

        $indikator = $this->getIndikator($namaIndikator);

        $this->assertDatabaseMissing('keterangan_indikators', [
            'indikator_id' => $indikator->id,
            'keterangan' => 'Keterangan Untuk Dihapus',
        ]);
    }

    public function test_admin_can_delete_indikator_tahap1(): void
    {
        $namaIndikator = 'Indikator Hapus ' . time();

        $this->browse(function (Browser $browser) use ($namaIndikator) {
            $this->freshLogin($browser, $this->admin());

            $this->createIndikator($browser, $namaIndikator);

            $this->clickRowButton(
                $browser,
                $namaIndikator,
                '.btn-hapus-indikator',
                '#tabelIndikatorBody'
            );

            $browser->waitFor('#btnHapusIndikator', 5)
                    ->pause(300)
                    ->click('#btnHapusIndikator')
                    ->waitForText('berhasil dihapus', 10);
        });

        $this->assertDatabaseMissing('indikators', [
            'nama_indikator' => $namaIndikator,
        ]);
    }
}