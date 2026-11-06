@extends('layouts.app')

@section('title', 'SMART BOOKS - Data Distributor')
@section('header_title', 'Data Distributor')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-[#191c1e]">Data Distributor</h2>
            <p class="text-xs md:text-sm text-[#434655] mt-1">Daftar mitra distributor dan penerbit buku.</p>
        </div>
        <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="px-5 py-2.5 bg-[#004ac6] text-white text-xs font-bold rounded-full hover:bg-blue-700 shadow-sm flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[18px]">add</span> Tambah Distributor
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-[#c3c6d7] shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-500 font-semibold">
                    <th class="py-3.5 px-4">Kode</th>
                    <th class="py-3.5 px-4">Nama Distributor</th>
                    <th class="py-3.5 px-4">Alamat</th>
                    <th class="py-3.5 px-4">Telepon</th>
                    <th class="py-3.5 px-4">Email</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($distributors as $d)
                <tr class="hover:bg-gray-50">
                    <td class="py-3.5 px-4 font-bold text-[#004ac6]">{{ $d->kode }}</td>
                    <td class="py-3.5 px-4 font-bold text-gray-900">{{ $d->nama }}</td>
                    <td class="py-3.5 px-4 text-gray-600">{{ $d->alamat }}</td>
                    <td class="py-3.5 px-4 font-mono text-gray-700">{{ $d->telepon }}</td>
                    <td class="py-3.5 px-4 text-blue-600">{{ $d->email }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-6 text-center text-gray-400">Belum ada data distributor.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($distributors->hasPages())
        <div>{{ $distributors->links() }}</div>
    @endif

    <!-- Modal Tambah -->
    <div id="modalAdd" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border space-y-4">
            <h3 class="font-bold text-sm text-gray-900">Tambah Distributor Baru</h3>
            <form action="{{ route('distributor.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div><label class="block font-bold mb-1">Nama Perusahaan *</label><input type="text" name="nama" required class="w-full p-2.5 bg-gray-50 border rounded-xl"></div>
                <div><label class="block font-bold mb-1">Alamat</label><textarea name="alamat" rows="2" class="w-full p-2.5 bg-gray-50 border rounded-xl"></textarea></div>
                <div class="grid grid-cols-2 gap-2">
                    <div><label class="block font-bold mb-1">Telepon</label><input type="text" name="telepon" class="w-full p-2.5 bg-gray-50 border rounded-xl"></div>
                    <div><label class="block font-bold mb-1">Email</label><input type="email" name="email" class="w-full p-2.5 bg-gray-50 border rounded-xl"></div>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')" class="px-4 py-2 font-bold text-gray-600 rounded-full">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-[#004ac6] text-white font-bold rounded-full shadow">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
