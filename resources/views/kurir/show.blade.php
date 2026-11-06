@extends('layouts.app')

@section('title', 'Detail Pengantaran - SMART BOOKS')
@section('header_title', 'Detail Pengantaran')

@section('content')
@php
    $mapQuery = $penjualan->latitude !== null && $penjualan->longitude !== null
        ? $penjualan->latitude . ',' . $penjualan->longitude
        : $penjualan->alamat_pengiriman;
@endphp
<div class="max-w-5xl mx-auto space-y-5">
    <a href="{{ route('kurir.dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-[#004ac6]"><span class="material-symbols-outlined text-base">arrow_back</span>Kembali ke daftar tugas</a>

    <section class="rounded-2xl bg-white border border-[#c3c6d7] shadow-sm overflow-hidden">
        <div class="bg-[#004ac6] p-6 md:p-7 text-white">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <p class="text-sm text-blue-100">Detail pesanan pengantaran</p>
                    <h1 class="mt-2 text-2xl md:text-3xl font-extrabold tracking-tight">{{ $penjualan->kode_transaksi }}</h1>
                    <p class="mt-2 text-xs text-blue-100">Dibuat {{ $penjualan->tanggal?->format('d M Y • H:i') ?? '-' }}</p>
                </div>
                <span class="self-start rounded-full bg-white/15 px-4 py-2 text-xs font-bold ring-1 ring-white/20">{{ $penjualan->status }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[1.1fr_.9fr] gap-0">
            <div class="p-5 md:p-7 space-y-6">
                <div>
                    <div class="flex items-center gap-2 mb-3"><span class="material-symbols-outlined text-[#004ac6]">person</span><h2 class="font-bold text-gray-900">Informasi penerima</h2></div>
                    <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                        <p class="font-bold text-gray-900">{{ $penjualan->nama_penerima ?: $penjualan->user?->name ?: '-' }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $penjualan->telepon_penerima ?: 'Nomor telepon belum tersedia' }}</p>
                        @if($penjualan->telepon_penerima)
                            @php($phone = preg_replace('/[^0-9+]/', '', $penjualan->telepon_penerima))
                            <div class="mt-3 flex flex-wrap gap-2">
                                <a href="tel:{{ $phone }}" class="inline-flex items-center gap-1.5 rounded-full bg-white border border-[#c3c6d7] px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-100"><span class="material-symbols-outlined text-base">call</span>Telepon</a>
                                <a target="_blank" rel="noopener" href="https://wa.me/{{ str_starts_with($phone, '0') ? '62' . substr($phone, 1) : ltrim($phone, '+') }}" class="inline-flex items-center gap-1.5 rounded-full bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700"><span class="material-symbols-outlined text-base">chat</span>WhatsApp</a>
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <div class="flex items-center gap-2 mb-3"><span class="material-symbols-outlined text-[#004ac6]">location_on</span><h2 class="font-bold text-gray-900">Lokasi tujuan</h2></div>
                    <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-4">
                        <p class="text-sm leading-relaxed font-semibold text-gray-800 whitespace-pre-line">{{ $penjualan->alamat_pengiriman ?: 'Alamat belum tersedia' }}</p>
                        @if($penjualan->latitude !== null && $penjualan->longitude !== null)
                            <div class="mt-3 rounded-xl border border-blue-100 bg-white/80 p-3">
                                <p class="text-[11px] font-bold text-[#004ac6]">Titik GPS pelanggan</p>
                                <p class="mt-1 text-xs font-semibold text-gray-700">{{ number_format((float) $penjualan->latitude, 6, '.', '') }}, {{ number_format((float) $penjualan->longitude, 6, '.', '') }}</p>
                            </div>
                        @else
                            <p class="mt-3 text-xs text-amber-700">Pelanggan belum membagikan titik GPS. Gunakan alamat tertulis.</p>
                        @endif
                        @if($penjualan->alamat_pengiriman || ($penjualan->latitude !== null && $penjualan->longitude !== null))
                            <a target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query={{ urlencode($mapQuery) }}" class="mt-3 inline-flex items-center justify-center gap-2 rounded-full bg-[#004ac6] px-5 py-2.5 text-xs font-bold text-white hover:bg-blue-700"><span class="material-symbols-outlined text-base">near_me</span>Buka navigasi Google Maps</a>
                        @endif
                    </div>
                </div>

                @if($penjualan->catatan)
                    <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4"><p class="text-[11px] font-bold text-amber-800">Catatan dari pelanggan</p><p class="mt-1 text-sm text-amber-950">{{ $penjualan->catatan }}</p></div>
                @endif
            </div>

            <aside class="bg-gray-50/80 border-t lg:border-t-0 lg:border-l border-gray-100 p-5 md:p-7 space-y-5">
                <div>
                    <div class="flex items-center gap-2 mb-3"><span class="material-symbols-outlined text-[#004ac6]">inventory_2</span><h2 class="font-bold text-gray-900">Barang yang diantar</h2></div>
                    <div class="rounded-2xl bg-white border border-[#c3c6d7] divide-y divide-gray-100">
                        @foreach($penjualan->details as $detail)
                            <div class="flex items-start justify-between gap-3 p-4 text-xs"><div class="min-w-0"><p class="font-bold text-gray-900">{{ $detail->buku?->judul ?? 'Buku' }}</p><p class="text-gray-500 mt-1">Jumlah: {{ $detail->jumlah }}</p></div><span class="font-bold whitespace-nowrap text-gray-800">@rupiah($detail->subtotal)</span></div>
                        @endforeach
                        <div class="flex items-center justify-between gap-3 p-4"><span class="text-xs font-bold text-gray-500">Total pesanan</span><span class="text-lg font-black text-[#004ac6]">@rupiah($penjualan->total_bayar)</span></div>
                    </div>
                </div>

                <div>
                    <h2 class="font-bold text-gray-900 mb-3">Progres pengantaran</h2>
                    <div class="rounded-2xl bg-white border border-[#c3c6d7] p-4 space-y-3">
                        <div class="flex items-center gap-3"><span class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center"><span class="material-symbols-outlined text-base">check</span></span><div><p class="text-xs font-bold text-gray-900">Pesanan diproses</p><p class="text-[11px] text-gray-500">Siap diserahkan kepada kurir</p></div></div>
                        <div class="ml-3.5 h-4 border-l-2 border-dashed border-[#c3c6d7]"></div>
                        <div class="flex items-center gap-3"><span class="w-7 h-7 rounded-full {{ in_array($penjualan->status, ['Dikirim', 'Completed'], true) ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center"><span class="material-symbols-outlined text-base">local_shipping</span></span><div><p class="text-xs font-bold text-gray-900">Dalam pengantaran</p><p class="text-[11px] text-gray-500">{{ $penjualan->dikirim_pada?->format('d M Y, H:i') ?? 'Belum dimulai' }}</p></div></div>
                        <div class="ml-3.5 h-4 border-l-2 border-dashed border-[#c3c6d7]"></div>
                        <div class="flex items-center gap-3"><span class="w-7 h-7 rounded-full {{ $penjualan->status === 'Completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center"><span class="material-symbols-outlined text-base">task_alt</span></span><div><p class="text-xs font-bold text-gray-900">Diterima pelanggan</p><p class="text-[11px] text-gray-500">{{ $penjualan->diterima_pada?->format('d M Y, H:i') ?? 'Menunggu konfirmasi penerimaan' }}</p></div></div>
                    </div>
                </div>

                @if($penjualan->status === 'Diproses')
                    <form method="POST" action="{{ route('kurir.pesanan.status', $penjualan) }}" onsubmit="return confirm('Mulai pengantaran pesanan ini?')" class="rounded-2xl border border-blue-100 bg-blue-50 p-4">
                        @csrf<input type="hidden" name="status" value="Dikirim">
                        <p class="text-sm font-bold text-gray-900">Siap berangkat?</p><p class="mt-1 text-xs text-gray-600">Mulai pengantaran setelah barang dibawa dan siap dikirim.</p>
                        <button class="mt-3 w-full rounded-full bg-[#004ac6] px-4 py-3 text-sm font-bold text-white hover:bg-blue-700">Mulai Pengantaran</button>
                    </form>
                @elseif($penjualan->status === 'Dikirim')
                    <form id="delivery-receipt-form" method="POST" action="{{ route('kurir.pesanan.status', $penjualan) }}" enctype="multipart/form-data" class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 space-y-3">
                        @csrf<input type="hidden" name="status" value="Completed">
                        <div><p class="text-sm font-bold text-gray-900">Pesanan sudah diterima?</p><p class="mt-1 text-xs leading-relaxed text-gray-600">Ambil foto barang saat diserahkan kepada penerima. Foto ini akan menjadi bukti pengiriman dan dapat dilihat pelanggan.</p></div>
                        <label for="bukti-penerimaan" class="block cursor-pointer rounded-xl border-2 border-dashed border-emerald-300 bg-white p-4 text-center hover:bg-emerald-50/60">
                            <span class="material-symbols-outlined text-3xl text-emerald-700">add_a_photo</span>
                            <span class="mt-1 block text-xs font-bold text-gray-800">Pilih foto bukti penerimaan</span>
                            <span class="mt-1 block text-[11px] text-gray-500">JPG, PNG, WEBP · Maksimal 5 MB</span>
                            <input id="bukti-penerimaan" type="file" name="bukti_penerimaan" accept="image/jpeg,image/png,image/webp" capture="environment" required class="sr-only">
                        </label>
                        <div id="receipt-preview-wrap" class="hidden"><img id="receipt-preview" alt="Pratinjau bukti penerimaan" class="max-h-56 w-full rounded-xl border border-emerald-100 bg-white object-contain"><p id="receipt-file-name" class="mt-1 text-[11px] text-gray-600"></p></div>
                        @error('bukti_penerimaan')<p class="text-xs font-semibold text-red-700">{{ $message }}</p>@enderror
                        <button id="complete-delivery-button" class="w-full rounded-full bg-emerald-600 px-4 py-3 text-sm font-bold text-white hover:bg-emerald-700">Upload Bukti & Selesaikan</button>
                    </form>
                @elseif($penjualan->status === 'Completed')
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                        <div class="flex items-center gap-2 text-emerald-800"><span class="material-symbols-outlined">verified</span><p class="text-sm font-black">Pengantaran selesai</p></div>
                        <p class="mt-1 text-xs text-emerald-800">{{ $penjualan->bukti_penerimaan_uploaded_at?->format('d M Y, H:i') ?? 'Bukti penerimaan tersimpan.' }}</p>
                        @if($penjualan->bukti_penerimaan_path)<p class="mt-2 text-xs text-emerald-800">Foto bukti penerimaan sudah tersimpan dan dapat dilihat pelanggan di detail pesanan.</p>@endif
                    </div>
                @endif
            </aside>
        </div>
    </section>
</div>

<script>
(() => {
    const input = document.getElementById('bukti-penerimaan');
    if (!input) return;
    const previewWrap = document.getElementById('receipt-preview-wrap');
    const preview = document.getElementById('receipt-preview');
    const fileName = document.getElementById('receipt-file-name');
    const form = document.getElementById('delivery-receipt-form');
    const submit = document.getElementById('complete-delivery-button');
    let objectUrl = null;

    input.addEventListener('change', () => {
        const file = input.files && input.files[0];
        if (!file) return;
        if (objectUrl) URL.revokeObjectURL(objectUrl);
        objectUrl = URL.createObjectURL(file);
        preview.src = objectUrl;
        fileName.textContent = `${file.name} · ${(file.size / 1024 / 1024).toFixed(2)} MB`;
        previewWrap.classList.remove('hidden');
    });

    form.addEventListener('submit', (event) => {
        if (!input.files || !input.files[0]) {
            event.preventDefault();
            alert('Pilih foto bukti penerimaan terlebih dahulu.');
            return;
        }
        if (input.files[0].size > 5 * 1024 * 1024) {
            event.preventDefault();
            alert('Ukuran foto maksimal 5 MB.');
            return;
        }
        if (!confirm('Pastikan barang sudah diterima pelanggan. Upload foto dan selesaikan pesanan?')) {
            event.preventDefault();
            return;
        }
        submit.disabled = true;
        submit.textContent = 'Mengunggah bukti...';
    });
})();
</script>
@endsection
