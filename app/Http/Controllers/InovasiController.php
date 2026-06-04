<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubEvent;

class InovasiController extends Controller
{
    public function riwayat()
    {
        $subEvents = SubEvent::with('event')->orderBy('tahun', 'desc')->get();
        return view('inovasi.riwayat', compact('subEvents'));
    }

    public function rekapNilai()
    {
        $subEvents = SubEvent::with('event')->orderBy('tahun', 'desc')->get();
        return view('inovasi.rekapnilai', compact('subEvents'));
    }

    public function usulanRiwayat($subEventId)
    {
        $subEvent     = SubEvent::with('event')->findOrFail($subEventId);
        $subEventNama = $subEvent->sub_event;
        $eventNama    = $subEvent->event->nama_event ?? '-';

        // Ganti dengan query real saat model Inovasi sudah ada
        $usulan = [];

        return view('inovasi.usulan_riwayat', compact('usulan', 'subEventNama', 'eventNama'));
    }

    public function usulanNilai($subEventId)
    {
        $subEvent     = SubEvent::with('event')->findOrFail($subEventId);
        $subEventNama = $subEvent->sub_event;
        $eventNama    = $subEvent->event->nama_event ?? '-';

        // Ganti dengan query real saat model Inovasi sudah ada
        $usulan = [];

        return view('inovasi.usulan_nilai', compact('usulan', 'subEventNama', 'eventNama'));
    }
}