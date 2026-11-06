@extends(Auth::user()->role === 'pelanggan' ? 'layouts.shop' : 'layouts.app')

@section('title', 'Profil Pengguna - SMART BOOKS')
@section('header_title', 'Profil Pengguna')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="bg-white p-6 rounded-2xl border border-[#c3c6d7] shadow-sm space-y-6">
        <div class="flex items-center gap-4">
            @if($user->avatar_url)
                <img src="{{ $user->avatar_url }}" class="w-16 h-16 rounded-full object-cover border" alt="Avatar">
            @else
                <div class="w-16 h-16 rounded-full bg-[#004ac6] text-white flex items-center justify-center text-2xl font-bold">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</div>
            @endif
            <div>
                <h3 class="font-bold text-base text-gray-900">{{ $user->name }}</h3>
                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-[#004ac6] mt-1">{{ strtoupper($user->role) }}</span>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div><label class="block font-bold mb-1">Nama Lengkap</label><input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full p-2.5 bg-gray-50 border rounded-xl"></div>
            <div><label class="block font-bold mb-1">Email</label><input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full p-2.5 bg-gray-50 border rounded-xl"></div>
            <div><label class="block font-bold mb-1">Nomor Telepon / WhatsApp</label><input type="text" name="telepon" value="{{ old('telepon', $user->telepon) }}" class="w-full p-2.5 bg-gray-50 border rounded-xl"></div>
            <div><label class="block font-bold mb-1">Alamat Pengiriman</label><textarea name="alamat" rows="3" class="w-full p-2.5 bg-gray-50 border rounded-xl">{{ old('alamat', $user->alamat) }}</textarea></div>
            <div class="border-t pt-3">
                <p class="font-bold text-gray-800 mb-2">Ganti Password (Opsional)</p>
                <div class="space-y-2">
                    <input type="password" name="current_password" placeholder="Password saat ini (wajib jika mengganti password)" class="w-full p-2.5 bg-gray-50 border rounded-xl">
                    <input type="password" name="password" placeholder="Password baru" class="w-full p-2.5 bg-gray-50 border rounded-xl">
                    <input type="password" name="password_confirmation" placeholder="Ulangi password baru" class="w-full p-2.5 bg-gray-50 border rounded-xl">
                </div>
            </div>
            <div class="flex justify-end pt-2">
                <button type="submit" class="px-6 py-2 bg-[#004ac6] text-white font-bold rounded-full shadow">Simpan Profil</button>
            </div>
        </form>
    </div>
</div>
@endsection
