<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'      => 'adminbapperida',
                'nama'      => 'Admin BAPPERIDA',
                'email'     => 'admin@bapperida.test',
                'password'  => Hash::make('password'),
                'hak_akses' => 'admin_bapperida',   // Super Admin (UC §2.3.a)
                'status'    => 'aktif',
            ],
            [
                'name'      => 'penilausatu',
                'nama'      => 'Penilai Satu',
                'email'     => 'penilai1@inovasi.test',
                'password'  => Hash::make('password'),
                'hak_akses' => 'penilai',            // Tim Penilai (UC §2.3.c)
                'status'    => 'aktif',
            ],
            [
                'name'      => 'pesertasatu',
                'nama'      => 'Peserta Satu',
                'email'     => 'peserta1@inovasi.test',
                'password'  => Hash::make('password'),
                'hak_akses' => 'peserta',            // Peserta / Inovator (UC §2.3.b)
                'status'    => 'aktif',
            ],
        ];

        foreach ($users as $u) {
            User::firstOrCreate(['email' => $u['email']], $u);
        }

        $this->command->info('UserSeeder: ' . count($users) . ' user (admin_bapperida, penilai, peserta) berhasil di-seed.');
    }
}