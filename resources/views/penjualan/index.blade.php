@extends('layouts.app')

@section('title', 'SMART BOOKS - Riwayat Penjualan')
@section('header_title', 'Riwayat Transaksi')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-[#191c1e]">Riwayat Penjualan</h2>
            <p class="text-xs md:text-sm text-[#434655] mt-1">Transaksi kasir dan pesanan online dari pelanggan.</p>
        </div>
        <a href="{{ route('pos.index') }}" class="px-5 py-2.5 bg-[#004ac6] text-white text-xs font-bold rounded-full hover:bg-blue-700 shadow-sm flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[18px]">point_of_sale</span> Transaksi Baru
        </a>
    </div>

    <div class="flex flex-wrap items-center gap-2 text-xs">
        @foreach(['' => 'Semua', 'Pending' => 'Menunggu pembayaran', 'Diproses' => 'Diproses', 'Dikirim' => 'Dikirim', 'Completed' => 'Selesai', 'Cancelled' => 'Dibatalkan'] as $val => $label)
            <a href="{{ route('penjualan.index', array_filter(['status' => $val, 'q' => request('q')])) }}"
               class="px-4 py-1.5 rounded-full font-semibold {{ request('status', '') === $val ? 'bg-[#004ac6] text-white' : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-100' }}">{{ $label }}</a>
        @endforeach
        <form method="GET" action="{{ route('penjualan.index') }}" class="ml-auto flex gap-2">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode transaksi..." class="rounded-full border-gray-300 text-xs px-4 py-1.5">
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-[#c3c6d7] shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-500 font-semibold">
                    <th class="py-3 px-4">Kode Transaksi</th>
                    <th class="py-3 px-4">Waktu</th>
                    <th class="py-3 px-4">Kasir / Pelanggan</th>
                    <th class="py-3 px-4">Metode</th>
                    <th class="py-3 px-4 text-right">Total Bayar</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($penjualans as $trx)
                <tr class="hover:bg-gray-50">
                    <td class="py-3.5 px-4 font-bold text-[#004ac6]">{{ $trx->kode_transaksi }}@if($trx->isOnline()) <span class="ml-1 px-1.5 py-0.5 rounded bg-blue-100 text-[#004ac6] text-[9px] font-bold">ONLINE</span>@endif</td>
                    <td class="py-3.5 px-4 text-gray-500">{{ $trx->tanggal }}</td>
                    <td class="py-3.5 px-4 font-medium">{{ $trx->user->name ?? 'Kasir' }}</td>
                    <td class="py-3.5 px-4"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100">{{ $trx->metode_pembayaran }}</span></td>
                    <td class="py-3.5 px-4 text-right font-bold text-gray-900">@rupiah($trx->total_bayar)</td>
                    <td class="py-3.5 px-4 text-center">
                        @include('laporan._status', ['status' => $trx->status])
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <a href="{{ route('penjualan.show', $trx) }}" class="text-blue-600 hover:underline font-bold">Detail</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="p-6 text-center text-gray-400">Tidak ada transaksi yang cocok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($penjualans->hasPages())
        <div>{{ $penjualans->links() }}</div>
    @endif
</div>
@endsection
