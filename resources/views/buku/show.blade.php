@extends('layouts.app')

@section('title', 'SMART BOOKS - Detail Buku')
@section('header_title', 'Detail Buku')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <nav class="flex gap-2 text-xs text-gray-500 font-medium">
        <a href="{{ route('buku.index') }}">Data Buku</a>
        <span>/</span>
        <span class="text-gray-900 font-bold">Detail Buku</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Left: Cover -->
        <div class="w-full lg:w-1/4 bg-white p-4 rounded-2xl border border-[#c3c6d7] flex flex-col items-center shadow-sm">
            <div class="w-full aspect-[3/4] bg-gray-100 rounded-xl overflow-hidden mb-4 border">
                <img src="{{ $buku->cover_url }}" alt="{{ $buku->judul }}" class="w-full h-full object-cover">
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                {{ ucfirst($buku->status) }}
            </span>
            <span class="text-xs text-gray-400 font-mono mt-2">{{ $buku->kode }}</span>
        </div>

        <!-- Right: Info -->
        <div class="w-full lg:w-3/4 space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-[#c3c6d7] shadow-sm">
                <h3 class="font-bold text-base mb-4 flex items-center gap-2 border-b pb-2">
                    <span class="material-symbols-outlined text-[#004ac6]">info</span> Informasi Dasar
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div><span class="text-gray-400 font-semibold uppercase">ISBN:</span><p class="text-sm font-bold font-mono">{{ $buku->isbn }}</p></div>
                    <div><span class="text-gray-400 font-semibold uppercase">Judul Buku:</span><p class="text-sm font-bold text-gray-900">{{ $buku->judul }}</p></div>
                    <div><span class="text-gray-400 font-semibold uppercase">Kategori:</span><p class="font-bold text-blue-600">{{ $buku->kategori }}</p></div>
                    <div><span class="text-gray-400 font-semibold uppercase">Penulis:</span><p class="font-medium text-gray-800">{{ $buku->penulis }}</p></div>
                    <div><span class="text-gray-400 font-semibold uppercase">Penerbit:</span><p class="font-medium text-gray-800">{{ $buku->penerbit }}</p></div>
                    <div><span class="text-gray-400 font-semibold uppercase">Tahun Terbit:</span><p class="font-medium text-gray-800">{{ $buku->tahun_terbit }}</p></div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-[#c3c6d7] shadow-sm">
                <h3 class="font-bold text-base mb-4 flex items-center gap-2 border-b pb-2">
                    <span class="material-symbols-outlined text-[#004ac6]">inventory_2</span> Inventori & Harga
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div class="bg-gray-50 p-4 rounded-xl border"><span class="text-gray-500">Harga Pokok</span><p class="text-base font-bold">@rupiah($buku->harga_pokok)</p></div>
                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-200"><span class="text-blue-600 font-semibold">Harga Jual</span><p class="text-base font-extrabold text-[#004ac6]">@rupiah($buku->harga_jual)</p></div>
                    <div class="bg-gray-50 p-4 rounded-xl border"><span class="text-gray-500">Stok Tersedia</span><p class="text-base font-bold text-gray-900">{{ $buku->stok }} Unit</p></div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-[#c3c6d7] shadow-sm">
                <h3 class="font-bold text-base mb-2 border-b pb-2">Sinopsis</h3>
                <p class="text-xs text-gray-700 leading-relaxed">{{ $buku->sinopsis ?? 'Belum ada sinopsis.' }}</p>
            </div>
        </div>
    </div>

    <div class="flex justify-between items-center bg-white p-4 rounded-2xl border">
        <a href="{{ route('buku.index') }}" class="px-5 py-2 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-full">← Kembali</a>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-5 py-2 text-xs font-bold border border-blue-600 text-[#004ac6] rounded-full hover:bg-blue-50">Cetak</button>
            <a href="{{ route('buku.edit', $buku) }}" class="px-6 py-2 text-xs font-bold bg-[#004ac6] text-white rounded-full hover:bg-blue-700 shadow">Edit Buku</a>
        </div>
    </div>
</div>
@endsection
