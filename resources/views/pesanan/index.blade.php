@extends('layouts.shop')

@section('title', 'Pesanan Saya - SMART BOOKS')

@section('content')
<h1 class="text-2xl font-bold mb-6">Pesanan saya</h1>

<div class="space-y-4">
    @forelse($pesanans as $p)
    <a href="{{ route('pesanan.show', $p) }}" class="block bg-white rounded-2xl border border-[#c3c6d7] p-5 hover:shadow-md transition">
        <div class="flex flex-wrap justify-between items-start gap-2 mb-2">
            <div>
                <p class="font-bold text-[#004ac6]">{{ $p->kode_transaksi }}</p>
                <p class="text-xs text-gray-500">{{ $p->tanggal->format('d M Y H:i') }} &bull; {{ $p->metode_pembayaran }}</p>
            </div>
            @include('pesanan._badge', ['status' => $p->status])
        </div>
        <p class="text-xs text-gray-600 line-clamp-1">
            {{ $p->details->map(fn ($d) => ($d->buku->judul ?? 'Buku') . ' (' . $d->jumlah . ')')->implode(', ') }}
        </p>
        <p class="text-right font-bold mt-2">@rupiah($p->total_bayar)</p>
    </a>
    @empty
    <div class="bg-white rounded-2xl border border-[#c3c6d7] p-12 text-center">
        <p class="text-gray-500 mb-5">Kamu belum punya pesanan.</p>
        <a href="{{ route('landing') }}" class="inline-block px-6 py-2.5 rounded-full bg-[#2563eb] text-white text-sm font-bold hover:bg-blue-700">Mulai belanja</a>
    </div>
    @endforelse
</div>

@if($pesanans->hasPages())<div class="mt-6">{{ $pesanans->links() }}</div>@endif
@endsection
