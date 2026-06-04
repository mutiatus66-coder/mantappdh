<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
        public function run(): void
    {
        User::create(['nama' => 'admin',           'email' => 'admin@gmail.com',          'status' => 'active', 'hak_akses' => 'admin_kecamatan',   'password' => bcrypt('kecamatan123')]);
        User::create(['nama' => 'staff',           'email' => 'staff@gmail.com',          'status' => 'active', 'hak_akses' => 'penilai',           'password' => bcrypt('penilai123')]);
        User::create(['nama' => 'customer',        'email' => 'customer@gmail.com',       'status' => 'active', 'hak_akses' => 'peserta',           'password' => bcrypt('peserta123')]);
        User::create(['nama' => 'Admin Bapperida', 'email' => 'admin@bapperida.go.id',    'status' => 'active', 'hak_akses' => 'admin_bapperida',   'password' => bcrypt('bapperida123')]);
        User::create(['nama' => 'Admin Bapperida', 'email' => 'admin@opd.go.id',          'status' => 'active', 'hak_akses' => 'admin_opd',         'password' => bcrypt('opd123')]);
    }
}
