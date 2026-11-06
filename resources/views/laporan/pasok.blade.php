@extends('layouts.app')

@section('title', 'Laporan Pasok - SMART BOOKS')
@section('header_title', 'Laporan Pasok Buku')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <form method="GET" action="{{ route('laporan.pasok') }}" class="flex flex-wrap items-end gap-3 text-xs">
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

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-[#c3c6d7]">
            <p class="text-xs text-gray-500 font-semibold">Total buku masuk</p>
            <p class="text-3xl font-extrabold text-[#004ac6]">{{ number_format($totalUnit) }} <span class="text-sm font-normal text-gray-500">eksemplar</span></p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-[#c3c6d7]">
            <p class="text-xs text-gray-500 font-semibold">Jumlah transaksi pasok</p>
            <p class="text-3xl font-extrabold">{{ number_format($totalTransaksi) }}</p>
        </div>
    </div>

    @if($perDistributor->isNotEmpty())
    <div class="bg-white rounded-2xl border border-[#c3c6d7] p-5">
        <h3 class="font-bold text-sm mb-3">Per distributor</h3>
        <div class="divide-y text-xs">
            @foreach($perDistributor as $row)
            <div class="flex justify-between py-2">
                <span>{{ $row->distributor->nama ?? 'Distributor dihapus' }}</span>
                <span class="font-bold">{{ number_format($row->total) }} eks.</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-[#c3c6d7] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#f2f4f6] text-gray-500">
                    <tr>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Distributor</th>
                        <th class="py-3 px-4">Buku</th>
                        <th class="py-3 px-4 text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($pasoks as $p)
                    <tr>
                        <td class="py-3 px-4 whitespace-nowrap">{{ $p->tanggal->format('d M Y') }}</td>
                        <td class="py-3 px-4">{{ $p->distributor->nama ?? '-' }}</td>
                        <td class="py-3 px-4 font-semibold">{{ $p->buku->judul ?? '-' }}</td>
                        <td class="py-3 px-4 text-right font-bold">{{ number_format($p->jumlah) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-10 text-center text-gray-400">Tidak ada pasokan pada rentang tanggal ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pasoks->hasPages())<div class="p-4 border-t">{{ $pasoks->links() }}</div>@endif
    </div>
</div>
@endsection
