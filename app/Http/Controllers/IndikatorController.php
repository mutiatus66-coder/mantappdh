<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndikatorController extends Controller
{
    public function tahap1()
    {
        $subEvents = session('sub_events', []);
        return view('indikator.tahap-1', compact('subEvents'));
    }

    public function tahap2()
    {
        $subEvents = session('sub_events', []);
        return view('indikator.tahap-2', compact('subEvents'));
    }
}