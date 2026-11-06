@extends('layouts.shop')

@section('title', $buku->judul . ' - SMART BOOKS')

@section('content')
<a href="{{ route('landing') }}" class="inline-flex items-center gap-1 text-sm text-[#004ac6] hover:underline mb-4">
    <span class="material-symbols-outlined text-base">arrow_back</span> Kembali ke katalog
</a>

<div class="bg-white rounded-2xl border border-[#c3c6d7] p-6 md:p-8 flex flex-col md:flex-row gap-8">
    <div class="w-full md:w-64 shrink-0">
        <div class="aspect-[3/4] bg-gray-100 rounded-xl overflow-hidden">
            <img src="{{ $buku->cover_url }}" alt="{{ $buku->judul }}" class="w-full h-full object-cover" onerror="this.style.display='none'">
        </div>
    </div>

    <div class="flex-1 min-w-0">
        <span class="inline-block px-2.5 py-0.5 rounded-full bg-blue-100 text-[#004ac6] text-xs font-semibold mb-2">{{ $buku->kategori }}</span>
        <h1 class="text-2xl md:text-3xl font-bold">{{ $buku->judul }}</h1>
        <p class="text-[#434655] mb-4">{{ $buku->penulis }}</p>

        <div class="mb-1">@if($buku->diskon > 0)<span class="block text-sm text-gray-400 line-through">@rupiah($buku->harga_jual)</span><span class="inline-block text-xs font-bold text-white bg-red-600 rounded px-2 py-1 mb-1">Diskon {{ $buku->diskon }}%</span>@endif<p class="text-3xl font-extrabold text-[#004ac6]">@rupiah($buku->harga_net)</p></div>
        <p class="text-xs mb-6 {{ $buku->stok > 0 ? 'text-emerald-700' : 'text-red-600' }}">
            {{ $buku->stok > 0 ? 'Stok tersedia: ' . $buku->stok : 'Stok habis' }}
        </p>

        @if($buku->stok > 0)
        <form action="{{ route('keranjang.add', $buku) }}" method="POST" class="flex items-center gap-3 mb-8">
            @csrf
            <input type="number" name="qty" value="1" min="1" max="{{ $buku->stok }}" class="w-20 rounded-lg border-gray-300 text-sm text-center">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#2563eb] text-white font-bold text-sm hover:bg-blue-700">
                <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span> Tambah ke keranjang
            </button>
        </form>
        @endif

        <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm mb-6">
            <div><dt class="text-xs text-gray-500">ISBN</dt><dd class="font-semibold">{{ $buku->isbn }}</dd></div>
            <div><dt class="text-xs text-gray-500">Penerbit</dt><dd class="font-semibold">{{ $buku->penerbit ?: '-' }}</dd></div>
            <div><dt class="text-xs text-gray-500">Tahun terbit</dt><dd class="font-semibold">{{ $buku->tahun_terbit }}</dd></div>
        </dl>

        @if($buku->sinopsis)
        <div>
            <h2 class="text-sm font-bold mb-1">Sinopsis</h2>
            <p class="text-sm text-[#434655] leading-relaxed">{{ $buku->sinopsis }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
