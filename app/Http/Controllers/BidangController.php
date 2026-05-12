<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
    // Ambil dari session, fallback ke static data SubEvent
    $subEvents = session('sub_events', [
        ['id' => 1, 'sub_event' => 'LOMBA INOTEK 2022'],
        ['id' => 2, 'sub_event' => 'LOMBA INOTEK (INOTEK AWARD) 2023'],
        ['id' => 3, 'sub_event' => 'PELAPORAN INOVASI DAERAH 2023'],
        ['id' => 4, 'sub_event' => 'LOMBA INOVASI DAN TEKNOLOGI 2024'],
        ['id' => 5, 'sub_event' => 'PELAPORAN INOVASI DAERAH 2024 & INODA AWARD 2025'],
        ['id' => 6, 'sub_event' => 'PAMERAN INOTEK 2025'],
        ['id' => 7, 'sub_event' => 'KOMPETISI INOVASI DIGITAL 2025'],
    ]);

    return view('master.bidang', [
        'subEvents'  => $subEvents,
        'bidangData' => self::$bidang,
    ]);
}

    // Tambahkan method-method ini nanti
    public function store(Request $request) { /* TODO */ }
    public function edit(int $id) { /* TODO */ }
    public function update(Request $request, int $id) { /* TODO */ }
    public function destroy(int $id) { /* TODO */ }
}