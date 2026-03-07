<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Tampilkan Halaman Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Set session agar middleware CekAdmin lolos
            session(['is_admin' => true]); 

            return redirect()->intended(route('admin.pelanggan'));
        }

        // --- TAMBAHKAN BARIS INI JUGA ---
        dd("GAGAL LOGIN! Username atau Password dianggap salah.");
        // --------------------------------

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ]);
    }

    // Proses Logout
    public function logout(Request $request)
    {
        session()->forget('is_admin');
        return redirect()->route('login');
    }
}