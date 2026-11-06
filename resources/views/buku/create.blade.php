@extends('layouts.app')

@section('title', 'SMART BOOKS - Tambah Buku')
@section('header_title', 'Tambah Buku Baru')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <nav class="flex gap-2 text-xs text-gray-500 font-medium">
        <a href="{{ route('buku.index') }}">Data Buku</a>
        <span>/</span>
        <span class="text-gray-900 font-bold">Tambah Buku</span>
    </nav>

    <h2 class="text-2xl font-bold text-[#191c1e]">Form Tambah Buku Baru</h2>

    @if($errors->any())
        <div class="p-4 rounded-xl bg-red-50 text-red-700 text-xs font-semibold border border-red-200">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('buku.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-4 bg-white p-5 rounded-2xl border border-[#c3c6d7] space-y-4">
                <label class="block text-xs font-bold text-gray-800">Cover Buku URL</label>
                <input type="text" name="cover_url" value="{{ old('cover_url', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBa4IMkxOUZLcP2lNuKi_Qd1P_-W5MquXtrXVJyHYa6Ec_0jvhis_aQZqGkQjEkaJOc0alUXy6TUlVyE0VM1s3i3vyLvN4X5CEXoK8IdI7XDz5t_7yxlR0yLly7RBNE3pE0AgLbUlx9hsoJMxfaYlSeQmrl7L9Zzkj_jktrTWmkiJ2-ZqI_dYA0KrGxXJ04pJF1vhlsUjsqcDfjkez7uPoomaNf4M_2z15bzVPTg7Cde7-Rn-Ce_us') }}" class="w-full text-xs p-2.5 bg-gray-50 border rounded-xl" placeholder="https://...">

                <div class="space-y-2 pt-2 border-t">
                    <label class="block text-xs font-bold text-gray-800">Status Buku</label>
                    <label class="flex items-center gap-2 text-xs cursor-pointer">
                        <input type="radio" name="status" value="publikasi" checked class="text-blue-600"> Dipublikasikan
                    </label>
                    <label class="flex items-center gap-2 text-xs cursor-pointer">
                        <input type="radio" name="status" value="draf" class="text-blue-600"> Draft
                    </label>
                </div>

                <div class="pt-2 border-t">
                    <label class="block text-xs font-bold text-gray-800 mb-1">Lokasi Rak</label>
                    <input type="text" name="lokasi_rak" value="{{ old('lokasi_rak', 'A1-05') }}" class="w-full text-xs p-2.5 bg-gray-50 border rounded-xl" placeholder="Contoh: A1-05">
                </div>
            </div>

            <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-[#c3c6d7] space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-bold mb-1">ISBN *</label>
                        <input type="text" name="isbn" value="{{ old('isbn') }}" required class="w-full p-2.5 bg-gray-50 border rounded-xl font-mono" placeholder="978-...">
                    </div>
                    <div>
                        <label class="block font-bold mb-1">Judul Buku *</label>
                        <input type="text" name="judul" value="{{ old('judul') }}" required class="w-full p-2.5 bg-gray-50 border rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold mb-1">Kategori *</label>
                        <select name="kategori" class="w-full p-2.5 bg-gray-50 border rounded-xl">
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold mb-1">Penulis *</label>
                        <input type="text" name="penulis" value="{{ old('penulis') }}" required class="w-full p-2.5 bg-gray-50 border rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold mb-1">Penerbit</label>
                        <input type="text" name="penerbit" value="{{ old('penerbit') }}" class="w-full p-2.5 bg-gray-50 border rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold mb-1">Tahun Terbit</label>
                        <input type="number" name="tahun_terbit" value="{{ old('tahun_terbit', 2024) }}" class="w-full p-2.5 bg-gray-50 border rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold mb-1">Harga Pokok (Rp) *</label>
                        <input type="number" name="harga_pokok" value="{{ old('harga_pokok', 50000) }}" required class="w-full p-2.5 bg-gray-50 border rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold mb-1">Harga Jual (Rp) *</label>
                        <input type="number" name="harga_jual" value="{{ old('harga_jual', 75000) }}" required class="w-full p-2.5 bg-gray-50 border rounded-xl font-bold text-blue-700">
                    </div>
                    <div>
                        <label class="block font-bold mb-1">Diskon (%)</label>
                        <input type="number" name="diskon" value="{{ old('diskon', 0) }}" min="0" max="100" class="w-full p-2.5 bg-gray-50 border rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold mb-1">Stok Awal</label>
                        <input type="number" name="stok" value="{{ old('stok', 10) }}" min="0" class="w-full p-2.5 bg-gray-50 border rounded-xl font-bold">
                    </div>
                </div>

                <div class="pt-2">
                    <label class="block text-xs font-bold mb-1">Sinopsis Buku</label>
                    <textarea name="sinopsis" rows="4" class="w-full text-xs p-2.5 bg-gray-50 border rounded-xl leading-relaxed">{{ old('sinopsis') }}</textarea>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t">
                    <a href="{{ route('buku.index') }}" class="px-5 py-2 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-full">Batal</a>
                    <button type="submit" class="px-6 py-2 bg-[#004ac6] text-white text-xs font-bold rounded-full hover:bg-blue-700 shadow">Simpan Buku</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
