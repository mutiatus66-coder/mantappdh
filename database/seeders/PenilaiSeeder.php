<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\SubEvent;
use App\Models\Penilai;

class PenilaiSeeder extends Seeder
{
    public function run(): void
    {
        $penilaiData = [
            ['nama' => 'Dr. Ahmad Fauzi, M.T.',    'email' => 'ahmad.fauzi@example.com'],
            ['nama' => 'Prof. Siti Rahayu, Ph.D.', 'email' => 'siti.rahayu@example.com'],
            ['nama' => 'Ir. Budi Santoso, M.Sc.',  'email' => 'budi.santoso@example.com'],
            ['nama' => 'Dr. Dewi Kusuma, M.Si.',   'email' => 'dewi.kusuma@example.com'],
            ['nama' => 'Drs. Hendra Wijaya, M.M.', 'email' => 'hendra.wijaya@example.com'],
            ['nama' => 'Dr. Rina Fitriani, M.Pd.', 'email' => 'rina.fitriani@example.com'],
        ];

        $subEvents = SubEvent::orderBy('tahun', 'desc')->take(2)->get();

        foreach ($subEvents as $index => $se) {
            $slice = array_slice($penilaiData, $index * 3, 3);
            foreach ($slice as $p) {
                // Buat user jika belum ada
                $user = User::firstOrCreate(
                    ['email' => $p['email']],
                    [
                        'nama'      => $p['nama'],
                        'password'  => Hash::make('password'),
                        'hak_akses' => 'penilai',
                        'status'    => 'aktif',
                    ]
                );

                Penilai::firstOrCreate(
                    ['email' => $p['email']],
                    [
                        'sub_event_id' => $se->id,
                        'nama'         => $p['nama'],
                        'email'        => $p['email'],
                        'user_id'      => $user->id,
                    ]
                );
            }
        }
    }
}