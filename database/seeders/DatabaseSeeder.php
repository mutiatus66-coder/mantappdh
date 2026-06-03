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
    User::factory()->create([
        'nama'      => 'kamu',
        'email'     => 'admin@admin.com',
        'hak_akses' => 'admin_bapperida',
    ]);

    $this->call(UserSeeder::class);
    $this->call([
        EventSeeder::class,
    ]);
}
}
