<?php

namespace Tests\Browser\Concerns;

use App\Models\User;

/**
 * Helper untuk ambil user seeded per role, biar test lain
 * tinggal panggil $this->admin(), $this->penilai(), $this->peserta()
 * tanpa nulis ulang query email tiap kali.
 *
 * Sesuaikan email di bawah kalau UserSeeder.php berubah.
 */
trait WithTestUsers
{
    protected function admin(): User
    {
        return User::where('email', 'admin@bapperida.test')->firstOrFail();
    }

    protected function penilai(): User
    {
        return User::where('email', 'penilai1@inovasi.test')->firstOrFail();
    }

    protected function peserta(): User
    {
        return User::where('email', 'peserta1@inovasi.test')->firstOrFail();
    }
}