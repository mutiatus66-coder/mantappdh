<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;

class PublicPengumumanController extends Controller
{
    public function index()
    {
        $pengumuman = Pengumuman::where('status', 'Published')
                                ->orderBy('created_at', 'desc')
                                ->paginate(9);

        return view('public.pengumuman.index', compact('pengumuman'));
    }

    public function show($id)
    {
        $pengumuman = Pengumuman::where('id', $id)
                                ->where('status', 'Published')
                                ->firstOrFail();

        return view('public.pengumuman.show', compact('pengumuman'));
    }
}