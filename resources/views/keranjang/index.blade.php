@extends('layouts.shop')

@section('title', 'Keranjang - SMART BOOKS')

@section('content')
<h1 class="text-2xl font-bold mb-6">Keranjang belanja</h1>

@if(empty($lines))
    <div class="bg-white rounded-2xl border border-[#c3c6d7] p-12 text-center">
        <span class="material-symbols-outlined text-5xl text-gray-300">shopping_cart</span>
        <p class="text-gray-500 mt-3 mb-5">Keranjangmu masih kosong.</p>
        <a href="{{ route('landing') }}" class="inline-block px-6 py-2.5 rounded-full bg-[#2563eb] text-white text-sm font-bold hover:bg-blue-700">Mulai belanja</a>
    </div>
@else
<div class="flex flex-col lg:flex-row gap-6">
    <div class="flex-1 bg-white rounded-2xl border border-[#c3c6d7] divide-y">
        @foreach($lines as $line)
        @php $b = $line['buku']; @endphp
        <div class="p-4 flex gap-4">
            <a href="{{ route('toko.show', $b) }}" class="w-16 h-24 shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                <img src="{{ $b->cover_url }}" alt="" class="w-full h-full object-cover" onerror="this.style.display='none'">
            </a>
            <div class="flex-1 min-w-0">
                <a href="{{ route('toko.show', $b) }}" class="font-bold text-sm line-clamp-2 hover:text-[#004ac6]">{{ $b->judul }}</a>
                <p class="text-xs text-gray-500">{{ $b->penulis }}</p>
                <p class="text-sm font-semibold text-[#004ac6] mt-1">@rupiah($line['harga'])</p>@if($b->diskon > 0)<p class="text-[11px] text-gray-400 line-through">@rupiah($b->harga_jual)</p><span class="text-[10px] text-red-600 font-bold">Diskon {{ $b->diskon }}%</span>@endif

                <div class="flex items-center gap-3 mt-3">
                    <form action="{{ route('keranjang.update', $b) }}" method="POST" class="flex items-center gap-2">
                        @csrf @method('PATCH')
                        <input type="number" name="qty" value="{{ $line['qty'] }}" min="1" max="{{ $b->stok }}" onchange="this.form.submit()" class="w-16 rounded-lg border-gray-300 text-xs text-center py-1">
                        <span class="text-[11px] text-gray-400">stok {{ $b->stok }}</span>
                    </form>
                    <form action="{{ route('keranjang.remove', $b) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="text-xs text-[#ba1a1a] hover:underline">Hapus</button>
                    </form>
                </div>
            </div>
            <p class="font-bold text-sm whitespace-nowrap">@rupiah($line['subtotal'])</p>
        </div>
        @endforeach
    </div>

    <aside class="w-full lg:w-80 bg-white rounded-2xl border border-[#c3c6d7] p-5 h-fit space-y-2 text-sm">
        <h2 class="font-bold mb-2">Ringkasan</h2>
        <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>@rupiah($totals['subtotal'])</span></div>
        <div class="flex justify-between text-gray-600"><span>PPN (11%)</span><span>@rupiah($totals['ppn'])</span></div>
        <div class="flex justify-between font-bold text-lg text-[#004ac6] pt-2 border-t"><span>Total</span><span>@rupiah($totals['total'])</span></div>

        @if(Auth::check() && Auth::user()->role !== 'pelanggan')
            <p class="text-xs text-gray-500 pt-2">Akun staf tidak bisa checkout. Masuk dengan akun pelanggan untuk memesan.</p>
        @else
            <a href="{{ route('checkout.index') }}" class="block text-center mt-3 py-3 rounded-full bg-[#2563eb] text-white font-bold hover:bg-blue-700">
                {{ Auth::check() ? 'Lanjut ke checkout' : 'Masuk untuk checkout' }}
            </a>
        @endif
        <a href="{{ route('landing') }}" class="block text-center text-xs text-[#004ac6] hover:underline pt-2">Lanjut belanja</a>
    </aside>
</div>
@endif
@endsection
