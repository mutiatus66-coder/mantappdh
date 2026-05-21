<?php

namespace App\Http\Controllers;

use App\Models\SubEvent;

class InovasiController extends Controller
{
    // Halaman Riwayat
    public function riwayat()
    {
        // Ambil semua sub_event dari database
        $subEvents = SubEvent::all();

        // Ubah format agar cocok dengan view (tambahkan total_usulan & dinilai dummy)
        $formatted = $subEvents->map(function ($item) {
            // Ekstrak tahun dari kolom 'tahun' (sudah ada)
            $tahun = $item->tahun;

            // Dummy: nanti ganti dengan hitungan dari usulan
            $total_usulan = 10;   // Contoh dummy
            $dinilai = rand(0, $total_usulan); // Contoh random

            return (object) [
                'id'            => $item->id,
                'nama'          => $item->sub_event,  // pastikan view menggunakan 'nama'
                'tahun'         => $tahun,
                'total_usulan'  => $total_usulan,
                'dinilai'       => $dinilai,
            ];
        });

        return view('inovasi.riwayat', ['subEvents' => $formatted]);
    }

    // Halaman Rekap Nilai (sama seperti riwayat, beda judul)
    public function rekapNilai()
    {
        $subEvents = SubEvent::all();

        $formatted = $subEvents->map(function ($item) {
            $total_usulan = 10;   // dummy
            $dinilai = rand(0, $total_usulan);

            return (object) [
                'id'            => $item->id,
                'nama'          => $item->sub_event,
                'tahun'         => $item->tahun,
                'total_usulan'  => $total_usulan,
                'dinilai'       => $dinilai,
            ];
        });

        return view('inovasi.rekapnilai', ['subEvents' => $formatted]);
    }

    // … method usulanRiwayat, usulanNilai tetap seperti sebelumnya
}
