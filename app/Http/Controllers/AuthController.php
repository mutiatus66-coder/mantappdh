<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            Log::info('LOGIN', ['user' => Auth::user()->email, 'ip' => $request->ip()]);
            return redirect()->intended('/index');
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->onlyInput('email');
    }
    public function register(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|min:8|confirmed',
            'captcha_verified' => 'accepted',
        ], [
            'captcha_verified.accepted' => 'Silakan centang captcha untuk melanjutkan.',
        ]);

        User::create([
            'nama'      => $request->name,
            'email'     => $request->email,
            'hak_akses' => 'peserta',
            'status'    => 'aktif',
            'password'  => Hash::make($request->password),
        ]);

        Log::info('REGISTER', ['email' => $request->email, 'ip' => $request->ip()]);

        return redirect()->route('sign-in')->with('success', 'Pendaftaran berhasil! Silahkan login.');
    }

    public function logout(Request $request)
    {
        Log::info('LOGOUT', ['user' => Auth::user()->email, 'ip' => $request->ip()]);

        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}