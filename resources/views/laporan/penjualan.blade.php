@extends('layouts.app')

@section('title', 'SMART BOOKS - Laporan Penjualan')
@section('header_title', 'Laporan Penjualan')

@section('content')
<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
    <form method="GET" action="{{ route('laporan.penjualan') }}" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Dari tanggal</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="rounded-lg border-gray-300 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Sampai tanggal</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="rounded-lg border-gray-300 text-sm">
        </div>
        <button class="px-5 py-2 rounded-full bg-[#004ac6] text-white text-sm font-semibold hover:bg-blue-700">Tampilkan</button>
    </form>
    <div class="bg-[#004ac6] text-white rounded-2xl px-6 py-4">
        <p class="text-xs opacity-80">Total omset (Completed)</p>
        <p class="text-2xl font-bold">@rupiah($totalOmset)</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-[#c3c6d7] overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-[#f2f4f6] text-xs text-[#434655]">
                <tr class="text-left">
                    <th class="py-3 px-4">Kode</th>
                    <th class="py-3 px-4">Tanggal</th>
                    <th class="py-3 px-4">Kasir / Pelanggan</th>
                    <th class="py-3 px-4">Item</th>
                    <th class="py-3 px-4">Metode</th>
                    <th class="py-3 px-4 text-right">Total</th>
                    <th class="py-3 px-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($penjualans as $p)
                <tr class="align-top">
                    <td class="py-3 px-4 font-semibold">{{ $p->kode_transaksi }}</td>
                    <td class="py-3 px-4 text-gray-600 whitespace-nowrap">{{ $p->tanggal->format('d M Y H:i') }}</td>
                    <td class="py-3 px-4">{{ $p->user->name ?? '-' }}</td>
                    <td class="py-3 px-4">
                        @foreach($p->details as $d)
                            <p class="text-xs">{{ $d->jumlah }}&times; {{ $d->buku->judul ?? 'Buku dihapus' }}</p>
                        @endforeach
                    </td>
                    <td class="py-3 px-4">{{ $p->metode_pembayaran }}</td>
                    <td class="py-3 px-4 text-right font-bold">@rupiah($p->total_bayar)</td>
                    <td class="py-3 px-4">@include('laporan._status', ['status' => $p->status])</td>
                </tr>
                @empty
                <tr><td colspan="7" class="py-12 text-center text-gray-500">Tidak ada transaksi pada rentang tanggal ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($penjualans->hasPages())
    <div class="p-4 border-t">{{ $penjualans->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
