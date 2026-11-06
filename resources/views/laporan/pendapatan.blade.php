@extends('layouts.app')

@section('title', 'Laporan Pendapatan - SMART BOOKS')
@section('header_title', 'Laporan Laba / Pendapatan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <form method="GET" action="{{ route('laporan.pendapatan') }}" class="flex flex-wrap items-end gap-3 text-xs">
            <div>
                <label class="block text-gray-500 mb-1">Dari tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="rounded-lg border-gray-300 text-xs">
            </div>
            <div>
                <label class="block text-gray-500 mb-1">Sampai tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="rounded-lg border-gray-300 text-xs">
            </div>
            <button class="px-5 py-2 rounded-full bg-[#004ac6] text-white font-bold hover:bg-blue-700">Tampilkan</button>
        </form>
        <button onclick="window.print()" class="px-5 py-2 bg-white border border-[#c3c6d7] text-xs font-bold rounded-full">Cetak laporan</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
        <div class="bg-white p-6 rounded-2xl border border-[#c3c6d7]">
            <p class="text-gray-500 font-semibold mb-1">Penjualan bersih (sebelum PPN)</p>
            <h3 class="text-2xl font-extrabold text-[#004ac6]">@rupiah($penjualanBersih)</h3>
            <p class="text-gray-400 mt-2">{{ number_format($jumlahTransaksi) }} transaksi selesai</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-[#c3c6d7]">
            <p class="text-gray-500 font-semibold mb-1">Harga pokok penjualan (HPP)</p>
            <h3 class="text-2xl font-extrabold text-amber-600">@rupiah($hpp)</h3>
            <p class="text-gray-400 mt-2">Harga pokok &times; jumlah terjual</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-[#c3c6d7]">
            <p class="text-gray-500 font-semibold mb-1">Laba kotor</p>
            <h3 class="text-2xl font-extrabold {{ $labaKotor >= 0 ? 'text-emerald-600' : 'text-red-600' }}">@rupiah($labaKotor)</h3>
            <p class="text-gray-400 mt-2">Margin {{ $margin }}%</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
        <div class="bg-white p-4 rounded-2xl border border-[#c3c6d7] flex justify-between"><span class="text-gray-500">Total diterima (termasuk PPN)</span><span class="font-bold">@rupiah($omset)</span></div>
        <div class="bg-white p-4 rounded-2xl border border-[#c3c6d7] flex justify-between"><span class="text-gray-500">PPN terkumpul</span><span class="font-bold">@rupiah($ppn)</span></div>
    </div>

    <div class="bg-white rounded-2xl border border-[#c3c6d7] overflow-hidden">
        <div class="p-4 border-b font-bold text-xs">Rincian per hari</div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#f2f4f6] text-gray-500">
                    <tr>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4 text-right">Transaksi</th>
                        <th class="py-3 px-4 text-right">Penjualan bersih</th>
                        <th class="py-3 px-4 text-right">Total diterima</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($harian as $h)
                    <tr>
                        <td class="py-3 px-4">{{ \Carbon\Carbon::parse($h->hari)->format('d M Y') }}</td>
                        <td class="py-3 px-4 text-right">{{ $h->trx }}</td>
                        <td class="py-3 px-4 text-right">@rupiah($h->bersih)</td>
                        <td class="py-3 px-4 text-right font-bold">@rupiah($h->omset)</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-10 text-center text-gray-400">Belum ada transaksi selesai pada rentang tanggal ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
