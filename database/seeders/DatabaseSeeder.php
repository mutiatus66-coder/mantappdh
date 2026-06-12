<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{
    \App\Models\User::truncate();

    User::factory()->create([
        'name'      => 'kamu',
        'nama'      => 'Admin',
        'email'     => 'admin@admin.com',
        'hak_akses' => 'admin_bapperida',
        'status'    => 'aktif',
    ]);

            $this->call([
            EventSeeder::class,
            SubEventSeeder::class,
            BidangSeeder::class,
            UserSeeder::class,
            PenilaiSeeder::class,
            InovatorSeeder::class,
            PengumumanSeeder::class,
            UsulanSeeder::class,
            IndikatorSeeder::class,  
        ]);

}
}
