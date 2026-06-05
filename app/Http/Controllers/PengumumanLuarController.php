<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;

class PengumumanLuarController extends Controller
{
    public function index()
    {
        $pengumuman = Pengumuman::where('status', 'Published')
                                ->orderBy('created_at', 'desc')
                                ->paginate(9);
        return view('pengumuman_luar.index', compact('pengumuman'));
    }

    public function show($id)
    {
        $pengumuman = Pengumuman::where('id', $id)
                                ->where('status', 'Published')
                                ->firstOrFail();
        return view('pengumuman_luar.show', compact('pengumuman'));
    }
}
