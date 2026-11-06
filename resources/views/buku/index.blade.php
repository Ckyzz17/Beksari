@extends('layouts.app')

@section('title', 'SMART BOOKS - Data Buku')
@section('header_title', 'Data Buku')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#191c1e]">Data Buku</h2>
            <p class="text-xs md:text-sm text-[#434655] mt-1">Kelola semua informasi buku, stok, dan harga.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('buku.create') }}" class="px-5 py-2.5 bg-[#004ac6] hover:bg-blue-700 text-white rounded-full text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[18px]">add</span> Tambah Buku
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-[#c3c6d7] shadow-sm overflow-hidden">
        <!-- Filter Toolbar -->
        <div class="p-4 border-b flex flex-col md:flex-row justify-between items-center gap-4">
            <form action="{{ route('buku.index') }}" method="GET" class="flex gap-2 w-full md:w-auto">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari buku, ISBN, penulis..." class="text-xs px-3 py-2 bg-gray-50 border rounded-xl w-64 outline-none focus:ring-1 focus:ring-blue-600">
                <select name="kategori" onchange="this.form.submit()" class="text-xs px-3 py-2 bg-gray-50 border rounded-xl outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('kategori') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-3 py-2 bg-[#004ac6] text-white text-xs rounded-xl font-bold">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-[#f8fafc] border-b text-[#737686] font-semibold">
                        <th class="py-3 px-3">Cover</th>
                        <th class="py-3 px-4">Kode</th>
                        <th class="py-3 px-4">Judul & ISBN</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Penulis</th>
                        <th class="py-3 px-4 text-center">Stok</th>
                        <th class="py-3 px-4 text-right">Harga Jual</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bukus as $buku)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-3">
                            <div class="w-10 h-14 bg-gray-100 rounded-lg overflow-hidden border">
                                <img src="{{ $buku->cover_url }}" alt="{{ $buku->judul }}" class="w-full h-full object-cover">
                            </div>
                        </td>
                        <td class="py-3 px-4 font-bold text-[#004ac6]">{{ $buku->kode }}</td>
                        <td class="py-3 px-4 max-w-xs">
                            <div class="font-bold text-gray-900 line-clamp-1">{{ $buku->judul }}</div>
                            <div class="text-[10px] text-gray-400 font-mono">{{ $buku->isbn }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-[#004ac6] text-[10px] font-bold">{{ $buku->kategori }}</span>
                        </td>
                        <td class="py-3 px-4 text-gray-700">{{ $buku->penulis }}</td>
                        <td class="py-3 px-4 text-center font-bold {{ $buku->stok <= 5 ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $buku->stok }}
                        </td>
                        <td class="py-3 px-4 text-right font-bold text-gray-900">@rupiah($buku->harga_jual)</td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('buku.show', $buku) }}" class="p-1.5 text-gray-500 hover:text-blue-600" title="Detail">
                                    <span class="material-symbols-outlined text-sm">visibility</span>
                                </a>
                                <a href="{{ route('buku.edit', $buku) }}" class="p-1.5 text-gray-500 hover:text-amber-600" title="Edit">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                </a>
                                <form action="{{ route('buku.destroy', $buku) }}" method="POST" onsubmit="return confirm('Hapus buku {{ $buku->judul }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-500 hover:text-rose-600" title="Hapus">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="p-6 text-center text-gray-400">Tidak ada data buku yang ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t">
            {{ $bukus->links() }}
        </div>
    </div>
</div>
@endsection
