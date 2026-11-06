@extends('layouts.app')

@section('title', 'SMART BOOKS - Pasok Buku')
@section('header_title', 'Pasok Buku')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-[#191c1e]">Riwayat Pasok Buku</h2>
            <p class="text-xs md:text-sm text-[#434655] mt-1">Catat penerimaan stok masuk dari distributor resmi.</p>
        </div>
        <a href="{{ route('pasok.create') }}" class="px-5 py-2.5 bg-[#004ac6] text-white text-xs font-bold rounded-full hover:bg-blue-700 shadow-sm flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[18px]">add</span> Pasok Buku Baru
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-[#c3c6d7] shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-500 font-semibold">
                    <th class="py-3 px-4">Tanggal</th>
                    <th class="py-3 px-4">Distributor</th>
                    <th class="py-3 px-4">Buku Yang Dipasok</th>
                    <th class="py-3 px-4 text-center">Jumlah Pasok</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pasoks as $p)
                <tr class="hover:bg-gray-50">
                    <td class="py-3.5 px-4 text-gray-500">{{ $p->tanggal->format('d M Y') }}</td>
                    <td class="py-3.5 px-4 font-bold text-gray-900">{{ $p->distributor->nama ?? '-' }}</td>
                    <td class="py-3.5 px-4 font-bold text-[#004ac6]">{{ $p->buku->judul ?? '-' }}</td>
                    <td class="py-3.5 px-4 text-center">
                        <span class="px-3 py-1 bg-blue-100 text-[#004ac6] rounded-full font-bold">+{{ $p->jumlah }} Unit</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="p-6 text-center text-gray-400">Belum ada riwayat pasok buku.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pasoks->hasPages())
        <div>{{ $pasoks->links() }}</div>
    @endif
</div>
@endsection
