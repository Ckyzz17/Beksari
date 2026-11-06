@extends('layouts.app')

@section('title', 'SMART BOOKS - Dashboard Admin')
@section('header_title', 'Dashboard Admin')

@section('content')
@php
    $namaDepan = \Illuminate\Support\Str::words(Auth::user()->name, 1, '');
    $tanggalHariIni = now()->locale('id')->translatedFormat('l, d F Y');
    $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');

    $tiles = [
        ['label' => 'Pendapatan hari ini', 'value' => $rp($pendapatanHariIni), 'icon' => 'payments', 'tone' => 'emerald', 'hint' => 'Hanya transaksi selesai'],
        ['label' => 'Pendapatan bulan ini', 'value' => $rp($totalPenjualanBulanIni), 'icon' => 'calendar_month', 'tone' => 'blue', 'hint' => 'Hanya transaksi selesai'],
        ['label' => 'Transaksi hari ini', 'value' => number_format($penjualanHariIni, 0, ',', '.'), 'icon' => 'shopping_cart', 'tone' => 'violet', 'hint' => 'Semua status'],
        ['label' => 'Pesanan online menunggu', 'value' => number_format($pesananPending, 0, ',', '.'), 'icon' => 'notifications_active', 'tone' => $pesananPending > 0 ? 'amber' : 'slate', 'hint' => 'Perlu diperiksa kasir', 'href' => route('pesanan-masuk.index', ['status' => 'Pending'])],
        ['label' => 'Total buku', 'value' => number_format($totalBuku, 0, ',', '.'), 'icon' => 'menu_book', 'tone' => 'blue', 'hint' => 'Judul terdaftar'],
        ['label' => 'Total stok', 'value' => number_format($totalStok, 0, ',', '.'), 'icon' => 'inventory_2', 'tone' => 'amber', 'hint' => 'Unit tersedia di rak'],
        ['label' => 'Distributor', 'value' => number_format($totalDistributor, 0, ',', '.'), 'icon' => 'local_shipping', 'tone' => 'slate', 'hint' => 'Mitra pemasok'],
        ['label' => 'Pengguna', 'value' => number_format($totalUser, 0, ',', '.'), 'icon' => 'group', 'tone' => 'slate', 'hint' => 'Semua peran'],
    ];
@endphp
<div class="space-y-6">
    <x-page-header title="Dashboard Admin" :subtitle="'Halo, ' . $namaDepan . '. Ringkasan operasional toko, ' . $tanggalHariIni . '.'">
        <x-slot:actions>
            <a href="{{ route('buku.create') }}" class="px-5 py-2.5 bg-white border border-[#c3c6d7] text-xs font-bold rounded-full hover:bg-gray-50 flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[18px]">add</span> Tambah Buku
            </a>
            <a href="{{ route('pos.index') }}" class="px-5 py-2.5 bg-[#004ac6] text-white text-xs font-bold rounded-full hover:bg-blue-700 flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[18px]">point_of_sale</span> POS Kasir
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach($tiles as $tile)
            <x-stat-tile
                :label="$tile['label']"
                :value="$tile['value']"
                :icon="$tile['icon']"
                :tone="$tile['tone']"
                :hint="$tile['hint']"
                :href="$tile['href'] ?? null" />
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Transaksi terbaru --}}
        <section class="xl:col-span-2 bg-white rounded-2xl border border-[#c3c6d7] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-[#c3c6d7] flex items-center justify-between gap-3">
                <h3 class="font-bold text-base text-[#191c1e]">Transaksi terbaru</h3>
                <a href="{{ route('penjualan.index') }}" class="text-xs text-[#004ac6] hover:underline font-semibold">Semua transaksi</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-semibold">
                            <th class="py-3 px-4">Kode</th>
                            <th class="py-3 px-4">Waktu</th>
                            <th class="py-3 px-4">Kasir / Pelanggan</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentTransactions as $trx)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3.5 px-4 font-bold text-[#004ac6] whitespace-nowrap">
                                    <a href="{{ route('penjualan.show', $trx) }}" class="hover:underline">{{ $trx->kode_transaksi }}</a>
                                    @if($trx->isOnline())
                                        <span class="ml-1 px-1.5 py-0.5 rounded bg-blue-100 text-[#004ac6] text-[10px] font-bold">Online</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-gray-500 whitespace-nowrap">{{ $trx->tanggal?->format('d M, H:i') }}</td>
                                <td class="py-3.5 px-4">{{ $trx->user->name ?? '-' }}</td>
                                <td class="py-3.5 px-4 text-center"><x-status-badge :status="$trx->status" /></td>
                                <td class="py-3.5 px-4 text-right font-bold text-gray-900">@rupiah($trx->total_bayar)</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-8 text-center text-gray-400">Belum ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Stok menipis --}}
        <section class="bg-white rounded-2xl border border-[#c3c6d7] shadow-sm overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-[#c3c6d7] flex items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-rose-600 font-bold text-base">
                    <span class="material-symbols-outlined text-[20px]">warning</span>
                    <h3>Stok hampir habis</h3>
                </div>
                <a href="{{ route('buku.index') }}" class="text-xs text-[#004ac6] hover:underline font-semibold">Semua buku</a>
            </div>
            <div class="flex-1 divide-y divide-gray-100">
                @forelse($lowStockBooks as $buku)
                    <div class="px-5 py-3 flex justify-between items-center gap-3">
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-gray-900 line-clamp-1">{{ $buku->judul }}</h4>
                            <p class="text-[11px] text-gray-500">{{ $buku->penulis }}</p>
                        </div>
                        <span class="shrink-0 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700">{{ $buku->stok }} tersisa</span>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-10 text-center">Seluruh stok buku dalam kondisi aman.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
