@extends('layouts.app')

@section('title', 'SMART BOOKS - Dashboard Kasir')
@section('header_title', 'Dashboard Kasir')

@section('content')
@php
    $namaDepan = \Illuminate\Support\Str::words(Auth::user()->name, 1, '');
    $tanggalHariIni = now()->locale('id')->translatedFormat('l, d F Y');
    $ikonMetode = ['Tunai' => 'payments', 'Kartu' => 'credit_card', 'QRIS' => 'qr_code_2'];
@endphp
<div class="space-y-6">
    <x-page-header title="Dashboard Kasir" :subtitle="'Halo, ' . $namaDepan . '. Hari ini ' . $tanggalHariIni . '.'">
        <x-slot:actions>
            <a href="{{ route('pesanan-masuk.index') }}" class="px-5 py-2.5 bg-white border border-[#c3c6d7] text-xs font-bold rounded-full hover:bg-gray-50 flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[18px]">shopping_bag</span> Pesanan Online
            </a>
            <a href="{{ route('pos.index') }}" class="px-5 py-2.5 bg-[#004ac6] text-white text-xs font-bold rounded-full hover:bg-blue-700 flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[18px]">point_of_sale</span> Buka POS Kasir
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Antrean kerja: hal yang menunggu tindakan kasir --}}
        <section class="lg:col-span-2 bg-white rounded-2xl border border-[#c3c6d7] shadow-sm overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-[#c3c6d7] flex items-center justify-between gap-3">
                <div>
                    <h3 class="font-bold text-base text-[#191c1e]">Pesanan online perlu diperiksa</h3>
                    <p class="text-xs text-[#434655] mt-0.5">Periksa pembayaran, lalu setujui agar pesanan bisa diproses.</p>
                </div>
                <span class="shrink-0 px-3 py-1 rounded-full text-xs font-bold {{ $pesananOnlinePending > 0 ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-600' }}">{{ $pesananOnlinePending }} pesanan</span>
            </div>

            <div class="flex-1 divide-y divide-gray-100">
                @forelse($pesananMenunggu as $pesanan)
                    @php
                        $nonTunai = $pesanan->metode_pembayaran !== 'COD';
                        $ikon = match ($pesanan->metode_pembayaran) { 'COD' => 'local_shipping', 'QRIS' => 'qr_code_2', default => 'account_balance' };
                    @endphp
                    <a href="{{ route('pesanan-masuk.show', $pesanan) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition-colors">
                        <span class="w-10 h-10 shrink-0 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                            <span class="material-symbols-outlined">{{ $ikon }}</span>
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-[#004ac6]">{{ $pesanan->kode_transaksi }}</p>
                            <p class="text-xs text-[#191c1e] truncate">{{ $pesanan->nama_penerima ?: 'Pelanggan' }}</p>
                            <p class="text-[11px] text-[#737686]">{{ $pesanan->metode_pembayaran }}, {{ $pesanan->tanggal?->locale('id')->diffForHumans() }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-[#191c1e]">@rupiah($pesanan->total_bayar)</p>
                            @if($nonTunai)
                                <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $pesanan->payment_proof_path ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $pesanan->payment_proof_path ? 'Bukti sudah diunggah' : 'Belum ada bukti' }}
                                </span>
                            @else
                                <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-[#004ac6]">Bayar di tempat</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="px-5 py-14 text-center">
                        <span class="material-symbols-outlined text-4xl text-emerald-600">check_circle</span>
                        <p class="mt-2 text-sm font-bold text-[#191c1e]">Tidak ada pesanan yang menunggu</p>
                        <p class="mt-1 text-xs text-[#434655]">Pesanan baru dari toko online akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>

            @if($pesananOnlinePending > $pesananMenunggu->count())
                <a href="{{ route('pesanan-masuk.index', ['status' => 'Pending']) }}" class="px-5 py-3 border-t border-[#c3c6d7] text-xs font-bold text-[#004ac6] hover:bg-gray-50 text-center">
                    Lihat semua {{ $pesananOnlinePending }} pesanan
                </a>
            @endif

            @if($perluKurir > 0)
                <a href="{{ route('pesanan-masuk.index', ['status' => 'Diproses']) }}" class="flex items-center gap-3 px-5 py-3 bg-blue-50 border-t border-blue-100 text-xs text-blue-900 hover:bg-blue-100 transition-colors">
                    <span class="material-symbols-outlined text-[20px] text-[#004ac6]">local_shipping</span>
                    <span class="flex-1">{{ $perluKurir }} pesanan sudah diproses dan belum punya kurir.</span>
                    <span class="font-bold text-[#004ac6]">Tugaskan kurir</span>
                </a>
            @endif
        </section>

        {{-- Ringkasan shift: satu-satunya blok berwarna solid di halaman ini --}}
        <aside class="rounded-2xl bg-[#004ac6] text-white p-6 flex flex-col shadow-sm">
            <p class="text-sm text-blue-100">Pendapatan kasir hari ini</p>
            <p class="mt-1 text-4xl font-extrabold tracking-tight break-words">@rupiah($pendapatanHariIni)</p>
            <p class="mt-2 text-xs text-blue-100">
                {{ $jumlahTransaksi }} transaksi selesai, rata-rata @rupiah($rataRataTransaksi) per transaksi
            </p>

            <div class="mt-6 space-y-4">
                @foreach($perMetode as $metode => $baris)
                    @php $persen = $pendapatanHariIni > 0 ? round($baris['total'] / $pendapatanHariIni * 100) : 0; @endphp
                    <div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">{{ $ikonMetode[$metode] }}</span>{{ $metode }}
                            </span>
                            <span class="font-bold">@rupiah($baris['total'])</span>
                        </div>
                        <div class="mt-1.5 h-1.5 rounded-full bg-white/20">
                            <div class="h-1.5 rounded-full bg-white" style="width: {{ $persen }}%"></div>
                        </div>
                        <p class="mt-1 text-[11px] text-blue-100">{{ $baris['jumlah'] }} transaksi</p>
                    </div>
                @endforeach
            </div>

            <p class="mt-auto pt-6 text-xs text-blue-100">
                Tunai dari penjualan hari ini: <span class="font-bold text-white">@rupiah($perMetode['Tunai']['total'])</span>
            </p>
        </aside>
    </div>

    {{-- Transaksi terbaru --}}
    <section class="bg-white rounded-2xl border border-[#c3c6d7] shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-[#c3c6d7]">
            <h3 class="font-bold text-base text-[#191c1e]">Transaksi terbaru hari ini</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b text-gray-500 font-semibold">
                        <th class="py-3 px-4">Kode</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Metode</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentTransactions as $trx)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3.5 px-4 font-bold text-[#004ac6]">{{ $trx->kode_transaksi }}</td>
                            <td class="py-3.5 px-4 text-gray-500 whitespace-nowrap">{{ $trx->tanggal?->format('H:i') }}</td>
                            <td class="py-3.5 px-4">{{ $trx->metode_pembayaran }}</td>
                            <td class="py-3.5 px-4 text-center"><x-status-badge :status="$trx->status" /></td>
                            <td class="py-3.5 px-4 text-right font-bold text-gray-900">@rupiah($trx->total_bayar)</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-400">
                                Belum ada transaksi hari ini. Mulai dari <a href="{{ route('pos.index') }}" class="font-bold text-[#004ac6] hover:underline">POS Kasir</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
