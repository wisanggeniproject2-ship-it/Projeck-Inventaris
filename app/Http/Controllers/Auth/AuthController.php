<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // ✅ UBAH VALIDASI: email → string (biar bisa input username)
        $credentials = $request->validate([
            'email' => 'required|string', // ❌ Hapus |email, ganti |string
            'password' => 'required',
        ]);

        // Tetap pake 'email' di Auth::attempt (karena kolom database tetap email)
        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun Anda tidak aktif.']);
            }
            
            // Redirect berdasarkan role (4 ROLE)
            return match($user->role) {
                'super_admin' => redirect()->route('super_admin.dashboard'),
                'admin_unit' => redirect()->route('admin_unit.dashboard'),
                'manager' => redirect()->route('manager.dashboard'),
                default => redirect()->route('user.dashboard'),
            };
        }

        // ✅ UBAH PESAN ERROR: email → username
        return back()->withErrors([
            'email' => 'Username atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('landing');
    }
}