@extends('layouts.app')

@section('title', 'SMART BOOKS - Data User')
@section('header_title', 'Data User & Staf')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-[#191c1e]">Manajemen Pengguna</h2>
            <p class="text-xs md:text-sm text-[#434655] mt-1">Kelola akun administrator, kasir, dan kurir.</p>
        </div>
        <button onclick="document.getElementById('modalUser').classList.remove('hidden')" class="px-5 py-2.5 bg-[#004ac6] text-white text-xs font-bold rounded-full hover:bg-blue-700 shadow-sm flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[18px]">person_add</span> Tambah User
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-[#c3c6d7] shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-500 font-semibold">
                    <th class="py-3 px-4">Nama</th>
                    <th class="py-3 px-4">Username</th>
                    <th class="py-3 px-4">Role</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($users as $u)
                <tr class="hover:bg-gray-50">
                    <td class="py-3.5 px-4 flex items-center gap-3">
                        <img src="{{ $u->avatar_url }}" class="w-9 h-9 rounded-full object-cover border" alt="Avatar">
                        <div>
                            <p class="font-bold text-gray-900">{{ $u->name }}</p>
                            <p class="text-gray-400 text-[10px]">{{ $u->email }}</p>
                        </div>
                    </td>
                    <td class="py-3.5 px-4 font-mono font-medium">{{ $u->username }}</td>
                    <td class="py-3.5 px-4">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $u->role === 'admin' ? 'bg-blue-100 text-blue-800' : ($u->role === 'kurir' ? 'bg-amber-100 text-amber-800' : 'bg-purple-100 text-purple-800') }}">
                            {{ strtoupper($u->role) }}
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        @if($u->id !== Auth::id())
                        <form action="{{ route('user.destroy', $u) }}" method="POST" onsubmit="return confirm('Hapus user {{ $u->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
                        </form>
                        @else
                        <span class="text-gray-400 text-[10px]">Akun Anda</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div>{{ $users->links() }}</div>
    @endif

    <div id="modalUser" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border space-y-4">
            <h3 class="font-bold text-sm text-gray-900">Tambah Akun Staf Baru</h3>
            <form action="{{ route('user.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div><label class="block font-bold mb-1">Nama Lengkap *</label><input type="text" name="name" required class="w-full p-2.5 bg-gray-50 border rounded-xl"></div>
                <div><label class="block font-bold mb-1">Username *</label><input type="text" name="username" required class="w-full p-2.5 bg-gray-50 border rounded-xl"></div>
                <div><label class="block font-bold mb-1">Email *</label><input type="email" name="email" required class="w-full p-2.5 bg-gray-50 border rounded-xl"></div>
                <div><label class="block font-bold mb-1">Password *</label><input type="password" name="password" required class="w-full p-2.5 bg-gray-50 border rounded-xl"></div>
                <div>
                    <label class="block font-bold mb-1">Peran / Role *</label>
                    <select name="role" class="w-full p-2.5 bg-gray-50 border rounded-xl">
                        <option value="kasir">Kasir</option>
                        <option value="admin">Administrator</option>
                        <option value="kurir">Kurir</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" onclick="document.getElementById('modalUser').classList.add('hidden')" class="px-4 py-2 font-bold text-gray-600 rounded-full">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-[#004ac6] text-white font-bold rounded-full shadow">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
