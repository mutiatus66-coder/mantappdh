<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndikatorController extends Controller
{
    public function index()
    {
        return view('indikator.tahap-1');
    }
}