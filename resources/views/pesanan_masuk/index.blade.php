@extends('layouts.app')

@section('title', 'SMART BOOKS - Pesanan Online')
@section('header_title', 'Pesanan Online')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3">
        <div>
            <h2 class="text-2xl font-bold text-[#191c1e]">Pesanan Masuk</h2>
            <p class="text-xs md:text-sm text-[#434655] mt-1">Kelola pesanan yang dibuat pelanggan melalui toko online.</p>
        </div>
    </div>

    <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-xs text-blue-900">
        <strong>Catatan pembayaran:</strong> pembayaran Transfer Bank dan QRIS belum diverifikasi otomatis. Periksa mutasi/rekening atau notifikasi QRIS terlebih dahulu sebelum menekan tombol konfirmasi. Untuk COD, setujui pesanan untuk diproses dan pembayaran dilakukan saat barang diterima.
    </div>

    <div class="flex flex-wrap items-center gap-2 text-xs">
        @foreach(['' => 'Semua', 'Pending' => 'Menunggu pembayaran', 'Diproses' => 'Diproses', 'Dikirim' => 'Dikirim', 'Completed' => 'Selesai', 'Cancelled' => 'Dibatalkan'] as $val => $label)
            <a href="{{ route('pesanan-masuk.index', array_filter(['status' => $val, 'q' => request('q')])) }}"
               class="px-4 py-1.5 rounded-full font-semibold {{ request('status', '') === $val ? 'bg-[#004ac6] text-white' : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-100' }}">{{ $label }}</a>
        @endforeach
        <form method="GET" action="{{ route('pesanan-masuk.index') }}" class="ml-auto flex gap-2">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode pesanan..." class="rounded-full border-gray-300 text-xs px-4 py-1.5">
            <button class="px-4 py-1.5 rounded-full bg-gray-800 text-white font-semibold">Cari</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-[#c3c6d7] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b text-gray-500 font-semibold">
                        <th class="py-3 px-4">Kode Pesanan</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Penerima</th>
                        <th class="py-3 px-4">Pembayaran</th>
                        <th class="py-3 px-4 text-right">Total</th>
                        <th class="py-3 px-4">Kurir</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($penjualans as $trx)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3.5 px-4 font-bold text-[#004ac6]">{{ $trx->kode_transaksi }}</td>
                        <td class="py-3.5 px-4 text-gray-500 whitespace-nowrap">{{ $trx->tanggal?->format('d/m/Y H:i') }}</td>
                        <td class="py-3.5 px-4">
                            <p class="font-semibold">{{ $trx->nama_penerima ?? $trx->user->name ?? '-' }}</p>
                            <p class="text-gray-500">{{ $trx->telepon_penerima ?? '-' }}</p>
                        </td>
                        <td class="py-3.5 px-4">{{ $trx->metode_pembayaran }}</td>
                        <td class="py-3.5 px-4 text-right font-bold">@rupiah($trx->total_bayar)</td>
                        <td class="py-3.5 px-4">{{ $trx->kurir?->name ?? 'Belum ditugaskan' }}</td>
                        <td class="py-3.5 px-4 text-center">@include('laporan._status', ['status' => $trx->status])</td>
                        <td class="py-3.5 px-4 text-center">
                            <a href="{{ route('pesanan-masuk.show', $trx) }}" class="inline-block px-3 py-1.5 rounded-full bg-blue-50 text-blue-700 font-bold hover:bg-blue-100">Periksa / Proses</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="p-8 text-center text-gray-400">Belum ada pesanan online yang cocok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($penjualans->hasPages())
        <div>{{ $penjualans->links() }}</div>
    @endif
</div>
@endsection
