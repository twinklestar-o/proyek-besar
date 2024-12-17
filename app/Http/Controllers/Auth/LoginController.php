<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('app.login');
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        // Cek apakah username ada di database
        $user = User::where('username', $username)->first();

        if (!$user) {
            // Jika username tidak ada, tampilkan error di form username
            return back()->withErrors([
                'username' => 'Username tidak ditemukan.',
            ])->withInput($request->only('username', 'remember'));
        }

        // Jika username ada, coba autentikasi password
        if (!Auth::attempt(['username' => $username, 'password' => $password], $request->filled('remember'))) {
            // Jika password salah, tampilkan error di form password
            return back()->withErrors([
                'password' => 'Password yang dimasukkan salah.',
            ])->withInput($request->only('username', 'remember'));
        }

        // Jika sukses login
        $request->session()->regenerate();
        return redirect()->intended('/admin/home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
