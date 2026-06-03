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
        User::create(['name' => 'admin',           'email' => 'admin@gmail.com',          'status' => 'active', 'hak_akses' => 'admin',           'password' => bcrypt('admin')]);
        User::create(['name' => 'staff',           'email' => 'staff@gmail.com',          'status' => 'active', 'hak_akses' => 'staff',           'password' => bcrypt('staff')]);
        User::create(['name' => 'customer',        'email' => 'customer@gmail.com',       'status' => 'active', 'hak_akses' => 'customer',        'password' => bcrypt('customer')]);
        User::create(['name' => 'Admin Bapperida', 'email' => 'admin@bapperida.go.id',    'status' => 'active', 'hak_akses' => 'admin_bapperida', 'password' => bcrypt('password123')]);
    }
}
