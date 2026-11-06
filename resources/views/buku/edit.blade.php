@extends('layouts.app')

@section('title', 'SMART BOOKS - Edit Buku')
@section('header_title', 'Edit Buku')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <nav class="flex gap-2 text-xs text-gray-500 font-medium">
        <a href="{{ route('buku.index') }}">Data Buku</a>
        <span>/</span>
        <span class="text-gray-900 font-bold">Edit Buku</span>
    </nav>

    <h2 class="text-2xl font-bold text-[#191c1e]">Edit Buku: {{ $buku->judul }}</h2>

    <form action="{{ route('buku.update', $buku) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-4 bg-white p-5 rounded-2xl border border-[#c3c6d7] space-y-4">
                <div class="aspect-[3/4] bg-gray-100 rounded-xl overflow-hidden border">
                    <img src="{{ $buku->cover_url }}" alt="Cover" class="w-full h-full object-cover">
                </div>
                <input type="text" name="cover_url" value="{{ old('cover_url', $buku->cover_url) }}" class="w-full text-xs p-2 bg-gray-50 border rounded-xl">

                <div class="pt-2 border-t text-xs space-y-2">
                    <label class="block font-bold">Status Buku</label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="status" value="publikasi" {{ $buku->status === 'publikasi' ? 'checked' : '' }}> Dipublikasikan
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="status" value="draf" {{ $buku->status === 'draf' ? 'checked' : '' }}> Draft
                    </label>
                </div>
            </div>

            <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-[#c3c6d7] space-y-4 text-xs">
                <div>
                    <label class="block font-bold mb-1 text-gray-500">ISBN (Permanen)</label>
                    <input type="text" value="{{ $buku->isbn }}" disabled class="w-full p-2.5 bg-gray-100 border rounded-xl font-mono font-bold text-gray-600 cursor-not-allowed">
                </div>
                <div>
                    <label class="block font-bold mb-1">Judul Buku *</label>
                    <input type="text" name="judul" value="{{ old('judul', $buku->judul) }}" required class="w-full p-2.5 bg-gray-50 border rounded-xl font-bold">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold mb-1">Kategori</label>
                        <select name="kategori" class="w-full p-2.5 bg-gray-50 border rounded-xl">
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ $buku->kategori === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold mb-1">Penulis</label>
                        <input type="text" name="penulis" value="{{ old('penulis', $buku->penulis) }}" required class="w-full p-2.5 bg-gray-50 border rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold mb-1">Harga Pokok (Rp)</label>
                        <input type="number" name="harga_pokok" value="{{ old('harga_pokok', $buku->harga_pokok) }}" required class="w-full p-2.5 bg-gray-50 border rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold mb-1">Harga Jual (Rp)</label>
                        <input type="number" name="harga_jual" value="{{ old('harga_jual', $buku->harga_jual) }}" required class="w-full p-2.5 bg-gray-50 border rounded-xl font-bold text-blue-700">
                    </div>
                    <div>
                        <label class="block font-bold mb-1">Stok</label>
                        <input type="number" name="stok" value="{{ old('stok', $buku->stok) }}" required class="w-full p-2.5 bg-gray-50 border rounded-xl font-bold">
                    </div>
                    <div>
                        <label class="block font-bold mb-1">Lokasi Rak</label>
                        <input type="text" name="lokasi_rak" value="{{ old('lokasi_rak', $buku->lokasi_rak) }}" class="w-full p-2.5 bg-gray-50 border rounded-xl">
                    </div>
                </div>

                <div>
                    <label class="block font-bold mb-1">Sinopsis</label>
                    <textarea name="sinopsis" rows="4" class="w-full p-2.5 bg-gray-50 border rounded-xl">{{ old('sinopsis', $buku->sinopsis) }}</textarea>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t">
                    <a href="{{ route('buku.show', $buku) }}" class="px-5 py-2 font-bold text-gray-600 hover:bg-gray-100 rounded-full">Batal</a>
                    <button type="submit" class="px-6 py-2 bg-[#004ac6] text-white font-bold rounded-full hover:bg-blue-700 shadow">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
