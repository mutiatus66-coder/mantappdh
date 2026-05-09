<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('master.user', compact('users'));
    }

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
            'password'  => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'User berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $id,
            'hak_akses' => 'required|in:admin,user',
            'status'    => 'required|in:aktif,nonaktif',
            'password'  => 'nullable|min:6',
        ]);

        $data = [
            'nama'      => $request->nama,
            'email'     => $request->email,
            'hak_akses' => $request->hak_akses,
            'status'    => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'User berhasil diperbarui!');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }

    public function loginAs($id)
    {
        $user = User::findOrFail($id);

        // Simpan ID admin asli ke session sebelum berpindah
        session(['admin_original_id' => Auth::id()]);

        Auth::login($user);

        return redirect('/')->with('success', 'Login sebagai ' . $user->nama);
    }
}