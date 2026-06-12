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
        // Simpan ID admin asli di session (hanya jika belum login-as sebelumnya)
        if (!session()->has('admin_original_id')) {
            session(['admin_original_id' => Auth::id()]);
        }

        $user = User::findOrFail($id);
        Auth::login($user);

        // Tetap di halaman user
        return redirect()->route('user.index')
                         ->with('success', 'Sedang login sebagai ' . $user->nama . ' (' . $user->hak_akses . ')');
    }

    public function loginBack()
    {
        $originalId = session('admin_original_id');

        if (!$originalId) {
            return redirect()->route('user.index')
                             ->with('error', 'Tidak ada sesi admin yang tersimpan.');
        }

        $admin = User::findOrFail($originalId);
        Auth::login($admin);
        session()->forget('admin_original_id');

        return redirect()->route('user.index')
                         ->with('success', 'Kembali ke akun ' . $admin->nama);
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}