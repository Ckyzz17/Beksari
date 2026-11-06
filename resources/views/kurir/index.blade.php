@extends('layouts.app')

@section('title', 'Tugas Pengantaran - SMART BOOKS')
@section('header_title', 'Pengantaran Saya')

@section('content')
@php
    $namaDepan = \Illuminate\Support\Str::words(Auth::user()->name, 1, '');
    $tanggalHariIni = now()->locale('id')->translatedFormat('l, d F Y');
    $berikutnya = $pesananAktif->first();
    $lainnya = $pesananAktif->slice(1);

    $linkPeta = fn ($p) => 'https://www.google.com/maps/search/?api=1&query=' . urlencode(
        $p->latitude !== null && $p->longitude !== null ? $p->latitude . ',' . $p->longitude : (string) $p->alamat_pengiriman
    );
@endphp
<div class="max-w-4xl mx-auto space-y-6">
    <x-page-header
        :title="'Halo, ' . $namaDepan"
        :subtitle="$tanggalHariIni . '. ' . ($pesananAktif->count() > 0 ? 'Kamu punya ' . $pesananAktif->count() . ' tugas pengantaran aktif.' : 'Belum ada tugas aktif.')" />

    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <x-stat-tile label="Tugas aktif" :value="$pesananAktif->count()" icon="delivery_dining" tone="blue" hint="Siap diantar dan sedang diantar" />
        <x-stat-tile label="Selesai hari ini" :value="$selesaiHariIni" icon="task_alt" tone="emerald" hint="Pengantaran dengan bukti foto" />
        <x-stat-tile class="col-span-2 sm:col-span-1" label="Tagihan COD" :value="'Rp ' . number_format($tagihanCod, 0, ',', '.')" icon="payments" tone="amber" hint="Tunai yang harus ditagih" />
    </div>

    @if($berikutnya)
        @php
            $sedangDiantar = $berikutnya->status === 'Dikirim';
            $cod = $berikutnya->metode_pembayaran === 'COD';
        @endphp
        {{-- Tujuan berikutnya: satu-satunya blok berwarna solid di halaman ini --}}
        <section class="rounded-2xl bg-[#004ac6] text-white p-5 md:p-6 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-sm text-blue-100">Tujuan berikutnya</p>
                    <h3 class="mt-1 text-xl md:text-2xl font-extrabold break-words">{{ $berikutnya->nama_penerima ?: $berikutnya->user?->name }}</h3>
                </div>
                <span class="shrink-0 px-3 py-1 rounded-full bg-white/15 text-xs font-bold">{{ $sedangDiantar ? 'Sedang diantar' : 'Siap diantar' }}</span>
            </div>

            <p class="mt-3 text-sm text-blue-50 line-clamp-3">{{ $berikutnya->alamat_pengiriman ?: 'Alamat belum tersedia' }}</p>

            <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-xs text-blue-100">
                <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-base">call</span>{{ $berikutnya->telepon_penerima ?: '-' }}</span>
                <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-base">receipt_long</span>{{ $berikutnya->kode_transaksi }}</span>
            </div>

            @if($cod)
                <div class="mt-4 rounded-xl bg-white/10 px-4 py-3 flex items-center justify-between gap-3 text-sm">
                    <span>Tagih tunai saat barang diterima</span>
                    <span class="font-extrabold">@rupiah($berikutnya->total_bayar)</span>
                </div>
            @endif

            <div class="mt-5 flex flex-col sm:flex-row gap-2">
                <a href="{{ $linkPeta($berikutnya) }}" target="_blank" rel="noopener" class="flex-1 inline-flex items-center justify-center gap-2 rounded-full bg-white text-[#004ac6] px-5 py-3 text-sm font-bold hover:bg-blue-50">
                    <span class="material-symbols-outlined text-[20px]">near_me</span>
                    {{ $berikutnya->latitude !== null ? 'Buka titik GPS' : 'Cari alamat di Maps' }}
                </a>
                <a href="{{ route('kurir.pesanan.show', $berikutnya) }}" class="flex-1 inline-flex items-center justify-center gap-2 rounded-full border border-white/40 px-5 py-3 text-sm font-bold hover:bg-white/10">
                    {{ $sedangDiantar ? 'Selesaikan pengantaran' : 'Buka tugas' }}
                </a>
            </div>
        </section>
    @else
        <div class="rounded-2xl border border-dashed border-[#c3c6d7] bg-white px-6 py-14 text-center">
            <span class="material-symbols-outlined text-4xl text-[#737686]">inventory_2</span>
            <h3 class="mt-2 font-bold text-[#191c1e]">Belum ada tugas pengantaran</h3>
            <p class="mt-1 text-sm text-[#434655]">Pesanan baru muncul di sini setelah kasir atau admin menugaskannya kepadamu.</p>
        </div>
    @endif

    @if($lainnya->isNotEmpty())
        <section class="space-y-3">
            <h3 class="font-bold text-base text-[#191c1e]">Tugas lainnya</h3>
            @foreach($lainnya as $pesanan)
                @php $dikirim = $pesanan->status === 'Dikirim'; @endphp
                <article class="rounded-2xl border border-[#c3c6d7] bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-bold text-[#004ac6]">{{ $pesanan->kode_transaksi }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $dikirim ? 'bg-indigo-100 text-indigo-800' : 'bg-blue-100 text-blue-800' }}">{{ $dikirim ? 'Sedang diantar' : 'Siap diantar' }}</span>
                                @if($pesanan->metode_pembayaran === 'COD')
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">COD</span>
                                @endif
                            </div>
                            <h4 class="mt-2 text-sm font-bold text-[#191c1e]">{{ $pesanan->nama_penerima ?: $pesanan->user?->name }}</h4>
                            <p class="mt-1 text-xs text-[#434655] line-clamp-2">{{ $pesanan->alamat_pengiriman ?: 'Alamat belum tersedia' }}</p>
                        </div>
                        <p class="shrink-0 text-sm font-bold text-[#191c1e]">@rupiah($pesanan->total_bayar)</p>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100 flex flex-col sm:flex-row gap-2 sm:justify-end">
                        <a href="{{ $linkPeta($pesanan) }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-full border border-[#c3c6d7] px-5 py-2.5 text-xs font-bold text-[#191c1e] hover:bg-gray-50">
                            <span class="material-symbols-outlined text-base text-[#004ac6]">map</span>
                            {{ $pesanan->latitude !== null ? 'Buka titik GPS' : 'Cari alamat di Maps' }}
                        </a>
                        <a href="{{ route('kurir.pesanan.show', $pesanan) }}" class="inline-flex items-center justify-center rounded-full bg-[#004ac6] px-5 py-2.5 text-xs font-bold text-white hover:bg-blue-700">Lihat detail tugas</a>
                    </div>
                </article>
            @endforeach
        </section>
    @endif

    {{-- Riwayat --}}
    <section class="bg-white rounded-2xl border border-[#c3c6d7] shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-[#c3c6d7]">
            <h3 class="font-bold text-base text-[#191c1e]">Riwayat pengantaran</h3>
            <p class="text-xs text-[#434655] mt-0.5">Sepuluh pesanan terakhir yang berhasil kamu selesaikan.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px] text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b text-gray-500 font-semibold">
                        <th class="py-3 px-4">Kode</th>
                        <th class="py-3 px-4">Penerima</th>
                        <th class="py-3 px-4">Waktu selesai</th>
                        <th class="py-3 px-4 text-center">Bukti foto</th>
                        <th class="py-3 px-4 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pesananSelesai as $pesanan)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3.5 px-4 font-bold text-[#004ac6]">{{ $pesanan->kode_transaksi }}</td>
                            <td class="py-3.5 px-4">{{ $pesanan->nama_penerima ?: $pesanan->user?->name }}</td>
                            <td class="py-3.5 px-4 text-gray-500 whitespace-nowrap">{{ $pesanan->diterima_pada?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $pesanan->bukti_penerimaan_path ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">{{ $pesanan->bukti_penerimaan_path ? 'Tersimpan' : 'Tidak ada' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-gray-900">@rupiah($pesanan->total_bayar)</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-8 text-center text-gray-400">Belum ada pengantaran yang selesai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
