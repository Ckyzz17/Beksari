<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('user.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,kasir,kurir',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['avatar_url'] = 'https://lh3.googleusercontent.com/aida-public/AB6AXuDt8H7hCnP121LXHMzskph4xwJ9wuJvhk36KITeOyt7gfhyRczpRid5jNgv_bxMkKeiHnpNO_PLTGL7i6gP8IaJr6JJmwKgPIuiyYRCKAnkLRRTchBHfSc6zi8zza-ZJG4NN-aJZ7VcV_3QLvdIBLWICQiky_mQrROdKpeOEoHgG6q0Tu5SplG1eTPMRTCUH5ix6xOkQ8F_DbVw6NQ4wTxZJ_EzT_3pVrdvbRfwuySLx7ZMalCigog';

        User::create($validated);
        return redirect()->route('user.index')->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        if ($user->role === 'kurir' && $user->assignedDeliveries()->whereIn('status', ['Diproses', 'Dikirim'])->exists()) {
            return back()->with('error', 'Kurir masih memiliki pengantaran aktif. Selesaikan atau alihkan pesanan terlebih dahulu.');
        }
        if ($user->penjualans()->exists()) {
            return back()->with('error', 'Akun memiliki riwayat transaksi. Nonaktifkan akun melalui prosedur admin; riwayat transaksi tidak boleh dihapus.');
        }
        $user->delete();
        return redirect()->route('user.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
