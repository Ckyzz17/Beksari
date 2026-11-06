<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect($this->homeFor(Auth::user()));
        }
        return view('auth.login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect($this->homeFor(Auth::user()));
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|alpha_dash|min:3|max:30|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip, dan underscore.',
            'username.unique' => 'Username sudah dipakai, coba yang lain.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Role selalu 'pelanggan' dan tidak pernah dibaca dari input.
        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'], // di-hash otomatis oleh cast 'hashed' di model User
            'role' => 'pelanggan',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('landing'))->with('success', 'Akun berhasil dibuat. Selamat berbelanja!');
    }

    private function homeFor($user): string
    {
        return match ($user->role) {
            'admin' => route('admin.dashboard'),
            'kasir' => route('kasir.dashboard'),
            'kurir' => route('kurir.dashboard'),
            default => route('landing'),
        };
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return match (Auth::user()->role) {
                'admin' => redirect()->intended(route('admin.dashboard'))->with('success', 'Selamat datang kembali, Admin!'),
                'kasir' => redirect()->intended(route('kasir.dashboard'))->with('success', 'Selamat bekerja!'),
                'kurir' => redirect()->intended(route('kurir.dashboard'))->with('success', 'Selamat bertugas mengantar pesanan!'),
                default => redirect()->intended(route('landing'))->with('success', 'Selamat datang, ' . Auth::user()->name . '!'),
            };
        }

        return back()->withErrors([
            'username' => 'Username atau password yang dimasukkan tidak cocok.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        $savedCart = $request->session()->get('cart', []);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        if (! empty($savedCart)) $request->session()->put('cart', $savedCart);

        return redirect()->route('login')->with('info', 'Anda telah berhasil keluar sistem.');
    }
}
