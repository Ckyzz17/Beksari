@extends('layouts.shop')

@section('title', 'SMART BOOKS - Temukan Buku Favoritmu')

@section('content')
<section class="text-center py-6 md:py-10">
    <h1 class="text-3xl md:text-5xl font-bold tracking-tight mb-3">Temukan buku favoritmu</h1>
    <p class="text-[#434655] max-w-xl mx-auto mb-6">Belanja buku online dari SMART BOOKS. Pilih buku, masukkan ke keranjang, dan kami antar ke alamatmu.</p>
    <form action="{{ route('landing') }}" method="GET" class="md:hidden max-w-md mx-auto">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul, penulis, atau ISBN..." class="w-full rounded-full border-gray-300 text-sm px-4 py-2.5">
    </form>
</section>

<section id="katalog">
    <div class="flex gap-2 overflow-x-auto pb-3 mb-5">
        @php $aktif = request('kategori', 'Semua'); @endphp
        <a href="{{ route('landing', array_filter(['q' => request('q')])) }}"
           class="px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap {{ $aktif === 'Semua' ? 'bg-[#004ac6] text-white' : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-100' }}">Semua</a>
        @foreach($categories as $cat)
            <a href="{{ route('landing', array_filter(['kategori' => $cat, 'q' => request('q')])) }}"
               class="px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap {{ $aktif === $cat ? 'bg-[#004ac6] text-white' : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-100' }}">{{ $cat }}</a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('landing') }}" class="flex justify-end mb-4">
        @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
        @if(request('kategori'))<input type="hidden" name="kategori" value="{{ request('kategori') }}">@endif
        <select name="urut" onchange="this.form.submit()" class="rounded-full border-gray-300 text-xs"><option value="terbaru" @selected(request('urut', 'terbaru') === 'terbaru')>Terbaru</option><option value="termurah" @selected(request('urut') === 'termurah')>Harga termurah</option><option value="termahal" @selected(request('urut') === 'termahal')>Harga termahal</option></select>
    </form>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Koleksi buku</h2>
        <span class="text-xs text-gray-500">{{ $books->total() }} buku</span>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
        @forelse($books as $buku)
        <div class="bg-white rounded-2xl border border-gray-200 p-3 md:p-4 flex flex-col hover:shadow-md transition">
            <a href="{{ route('toko.show', $buku) }}" class="block aspect-[3/4] bg-gray-100 rounded-xl overflow-hidden mb-3">
                <img src="{{ $buku->cover_url }}" alt="{{ $buku->judul }}" loading="lazy" class="w-full h-full object-cover" onerror="this.style.display='none'">
            </a>
            <span class="text-[11px] text-blue-600 font-semibold">{{ $buku->kategori }}</span>
            <a href="{{ route('toko.show', $buku) }}" class="font-bold text-sm line-clamp-2 mt-1 hover:text-[#004ac6]">{{ $buku->judul }}</a>
            <p class="text-xs text-gray-500 line-clamp-1">{{ $buku->penulis }}</p>
            <div class="mt-auto pt-3 flex items-end justify-between gap-2">
                <div>
                    @if($buku->diskon > 0)<span class="block text-[11px] text-gray-400 line-through">@rupiah($buku->harga_jual)</span><span class="inline-block text-[10px] font-bold text-white bg-red-600 rounded px-1 mb-1">-{{ $buku->diskon }}%</span>@endif
                    <span class="font-bold text-blue-700">@rupiah($buku->harga_net)</span>
                    @if($buku->stok > 0 && $buku->stok <= 5)
                        <p class="text-[11px] text-red-600">Sisa {{ $buku->stok }}</p>
                    @endif
                </div>
                @if($buku->stok > 0)
                <form action="{{ route('keranjang.add', $buku) }}" method="POST">
                    @csrf
                    <button type="submit" title="Tambah ke keranjang" class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center hover:bg-[#2563eb] hover:text-white transition">
                        <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
                    </button>
                </form>
                @else
                    <span class="text-xs font-semibold text-gray-400">Habis</span>
                @endif
            </div>
        </div>
        @empty
        <p class="col-span-full text-center text-gray-500 py-16">Tidak ada buku yang cocok dengan pencarianmu.</p>
        @endforelse
    </div>

    @if($books->hasPages())
        <div class="mt-8">{{ $books->links() }}</div>
    @endif
</section>
@endsection
