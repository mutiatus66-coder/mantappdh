<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // Menampilkan halaman daftar user
    public function index()
    {
        $users = User::all();
        return view('user.index', compact('users'));
    }

    // ⬇️ Taruh di sini
    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'hak_akses' => 'required|in:admin,user',
            'status'    => 'required|in:aktif,nonaktif',
            'password'  => 'required|min:6',
        ]);

        User::create([
            'nama'      => $request->nama,
            'email'     => $request->email,
            'hak_akses' => $request->hak_akses,
            'status'    => $request->status,
            'password'  => bcrypt($request->password),
        ]);

        return redirect()->back()->with('success', 'Data berhasil ditambahkan!');
    }
}