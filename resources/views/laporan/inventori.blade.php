@extends('layouts.app')

@section('title', 'SMART BOOKS - Laporan Inventori')
@section('header_title', 'Laporan Inventori')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-[#c3c6d7] p-6">
        <p class="text-sm text-[#434655]">Total stok semua buku</p>
        <p class="text-3xl font-bold mt-1">{{ number_format($totalStok) }} <span class="text-base font-normal text-gray-500">eksemplar</span></p>
    </div>
    <div class="bg-[#004ac6] text-white rounded-2xl p-6">
        <p class="text-sm opacity-80">Nilai aset (harga pokok &times; stok)</p>
        <p class="text-3xl font-bold mt-1">@rupiah($totalNilaiAset ?? 0)</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-[#c3c6d7] overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-[#f2f4f6] text-xs text-[#434655]">
                <tr class="text-left">
                    <th class="py-3 px-4">Kode</th>
                    <th class="py-3 px-4">Judul</th>
                    <th class="py-3 px-4">Kategori</th>
                    <th class="py-3 px-4">Rak</th>
                    <th class="py-3 px-4 text-right">Harga Pokok</th>
                    <th class="py-3 px-4 text-right">Stok</th>
                    <th class="py-3 px-4 text-right">Nilai</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($bukus as $b)
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 font-semibold">{{ $b->kode }}</td>
                    <td class="py-3 px-4"><a href="{{ route('buku.show', $b) }}" class="hover:text-[#004ac6]">{{ $b->judul }}</a></td>
                    <td class="py-3 px-4">{{ $b->kategori }}</td>
                    <td class="py-3 px-4">{{ $b->lokasi_rak ?: '-' }}</td>
                    <td class="py-3 px-4 text-right">@rupiah($b->harga_pokok)</td>
                    <td class="py-3 px-4 text-right">
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $b->stok <= 3 ? 'bg-red-100 text-red-700' : ($b->stok <= 10 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700') }}">{{ $b->stok }}</span>
                    </td>
                    <td class="py-3 px-4 text-right font-semibold">@rupiah($b->harga_pokok * $b->stok)</td>
                </tr>
                @empty
                <tr><td colspan="7" class="py-12 text-center text-gray-500">Belum ada data buku.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bukus->hasPages())
    <div class="p-4 border-t">{{ $bukus->links() }}</div>
    @endif
</div>
<p class="text-xs text-gray-500 mt-3">Diurutkan dari stok paling sedikit.</p>
@endsection
