<?php

namespace Tests\Browser;

use App\Models\Penilai;
use App\Models\SubEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;

class PenilaiCrudTest extends DuskTestCase
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
     * Cari Sub Event yang masih memiliki minimal sejumlah user penilai
     * yang belum terdaftar.
     */
    protected function getSubEventWithAvailableUsers(int $jumlah): array
    {
        $users = User::where('hak_akses', 'penilai')
            ->orderBy('id')
            ->get();

        foreach (SubEvent::orderBy('id')->get() as $subEvent) {

            $usedIds = Penilai::where('sub_event_id', $subEvent->id)
                ->pluck('user_id');

            $available = $users->whereNotIn('id', $usedIds)->values();

            if ($available->count() >= $jumlah) {
                return [$subEvent, $available];
            }
        }

        $this->fail(
            "Tidak ditemukan Sub Event dengan {$jumlah} user penilai yang tersedia."
        );
    }

    /**
     * Helper membuka halaman detail penilai.
     */
    protected function openDetail(Browser $browser, int $subEventId): void
    {
        $browser->visit('/penilai/' . $subEventId)
                ->pause(700)
                ->waitFor('#btnTambahPenilai', 5);
    }

    /**
     * Tambah penilai melalui modal.
     */
    protected function createPenilai(
        Browser $browser,
        int $subEventId,
        int $userId
    ): void {
        $this->openDetail($browser, $subEventId);

        $browser->click('#btnTambahPenilai')
                ->waitFor('#penilaiUserId', 5)
                ->select('#penilaiUserId', (string) $userId)
                ->pause(300)
                ->click('#btnSimpanPenilai')
                ->waitForText('berhasil ditambahkan', 10)
                ->pause(700);
    }

    /**
     * Search DataTables lalu klik tombol aksi.
     */
    protected function filterTableAndClick(
        Browser $browser,
        string $searchText,
        string $buttonClass
    ): void {
        $browser->type('input[type=search]', $searchText)
                ->pause(800);

        $browser->driver->executeScript("
            const btn = document.querySelector('$buttonClass');
            if (btn) btn.click();
        ");

        $browser->pause(500);
    }

    public function test_admin_can_create_penilai(): void
    {
        [$subEvent, $users] = $this->getSubEventWithAvailableUsers(1);

        $user = $users->first();

        $this->browse(function (Browser $browser) use ($subEvent, $user) {

            $this->freshLogin($browser, $this->admin());

            $this->createPenilai(
                $browser,
                $subEvent->id,
                $user->id
            );
        });

        $this->assertDatabaseHas('penilai', [
            'sub_event_id' => $subEvent->id,
            'user_id'      => $user->id,
            'nama'         => $user->nama,
        ]);
    }

    public function test_admin_can_edit_penilai(): void
    {
        [$subEvent, $users] = $this->getSubEventWithAvailableUsers(2);

        $userLama = $users[0];
        $userBaru = $users[1];

        $this->browse(function (Browser $browser)
            use ($subEvent, $userLama, $userBaru) {

            $this->freshLogin($browser, $this->admin());

            $this->createPenilai(
                $browser,
                $subEvent->id,
                $userLama->id
            );

            $this->filterTableAndClick(
                $browser,
                $userLama->nama,
                '.btn-edit-penilai'
            );

            $browser->waitFor('#penilaiUserId', 5)
                    ->select('#penilaiUserId', (string) $userBaru->id)
                    ->pause(300)
                    ->click('#btnSimpanPenilai')
                    ->waitForText('berhasil diganti', 10);
        });

        $this->assertDatabaseHas('penilai', [
            'sub_event_id' => $subEvent->id,
            'user_id'      => $userBaru->id,
            'nama'         => $userBaru->nama,
        ]);

        $this->assertDatabaseMissing('penilai', [
            'sub_event_id' => $subEvent->id,
            'user_id'      => $userLama->id,
        ]);
    }

    public function test_admin_can_delete_penilai(): void
    {
        [$subEvent, $users] = $this->getSubEventWithAvailableUsers(1);

        $user = $users->first();

        $this->browse(function (Browser $browser) use ($subEvent, $user) {

            $this->freshLogin($browser, $this->admin());

            $this->createPenilai(
                $browser,
                $subEvent->id,
                $user->id
            );

            $this->filterTableAndClick(
                $browser,
                $user->nama,
                '.btn-hapus-penilai'
            );

            $browser->waitFor('#btnHapusPenilai', 5)
                    ->pause(300)
                    ->click('#btnHapusPenilai')
                    ->waitForText('berhasil dihapus', 10);
        });

        $this->assertDatabaseMissing('penilai', [
            'sub_event_id' => $subEvent->id,
            'user_id'      => $user->id,
        ]);
    }
}