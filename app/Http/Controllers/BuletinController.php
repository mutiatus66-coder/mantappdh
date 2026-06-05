<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;

class BuletinController extends Controller
{
    public function index()
    {
        $buletin = Pengumuman::query()->where('status', 'Published')
                                ->latest('created_at')
                                ->paginate(9);
        return view('public.pengumuman.index', compact('buletin'));
    }

    public function show($id)
    {
        $buletin = Pengumuman::query()->where('status', 'Published')
                            ->findOrFail($id);
        return view('public.pengumuman.show', compact('buletin'));
    }
}