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
            'hak_akses' => 'required|in:admin_bapperida,peserta,penilai',
            'status'    => 'required|in:aktif,nonaktif',
            'password'  => 'required|min:6',
        ]);

        $user = User::create([
            'nama'      => $request->nama,
            'name'      => $request->nama,
            'email'     => $request->email,
            'hak_akses' => $request->hak_akses,
            'status'    => $request->status,
            'password'  => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'user'    => array_merge($user->toArray(), [
                'update_url'  => route('user.update', $user->id),
                'destroy_url' => route('user.destroy', $user->id),
                'login_url'   => route('user.login-as', $user->id),
            ]),
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $id,
            'hak_akses' => 'required|in:admin_bapperida,peserta,penilai',
            'status'    => 'required|in:aktif,nonaktif',
            'password'  => 'nullable|min:6',
        ]);

        $data = [
            'nama'      => $request->nama,
            'name'      => $request->nama,
            'email'     => $request->email,
            'hak_akses' => $request->hak_akses,
            'status'    => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return response()->json(['success' => true]);
    }

    public function loginAs($id)
    {
        $user = User::findOrFail($id);
        session(['admin_original_id' => Auth::id()]);
        Auth::login($user);
        return redirect('/')->with('success', 'Login sebagai ' . $user->nama);
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}