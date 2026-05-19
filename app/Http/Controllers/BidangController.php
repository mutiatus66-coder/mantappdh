<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubEvent;

class BidangController extends Controller
{
    private static array $bidang = [
        1 => [
            ['id' => 1, 'nama' => 'Teknologi Informasi', 'status' => 'aktif'],
            ['id' => 2, 'nama' => 'Pertanian',            'status' => 'tidak_aktif'],
        ],
        2 => [
            ['id' => 3, 'nama' => 'Kesehatan',            'status' => 'aktif'],
            ['id' => 4, 'nama' => 'Pendidikan',           'status' => 'aktif'],
        ],
        3 => [
            ['id' => 5, 'nama' => 'Lingkungan Hidup',     'status' => 'aktif'],
            ['id' => 6, 'nama' => 'Energi Terbarukan',    'status' => 'tidak_aktif'],
        ],
        4 => [
            ['id' => 7,  'nama' => 'Teknologi Informasi',  'status' => 'aktif'],
            ['id' => 8,  'nama' => 'Kesehatan',            'status' => 'aktif'],
            ['id' => 9,  'nama' => 'Pertanian',            'status' => 'aktif'],
            ['id' => 10, 'nama' => 'Pendidikan',           'status' => 'aktif'],
            ['id' => 11, 'nama' => 'Lingkungan Hidup',     'status' => 'aktif'],
        ],
    ];

    public function index()
    {
        return view('master.bidang', [
            'subEvents'  => SubEvent::getStaticData(), // ✅ panggil dari model
            'bidangData' => self::$bidang,
        ]);
    }

    // Tambahkan method-method ini nanti
    public function store(Request $request) { /* TODO */ }
    public function edit(int $id) { /* TODO */ }
    public function update(Request $request, int $id) { /* TODO */ }
    public function destroy(int $id) { /* TODO */ }
}