
@extends('layouts.shop')

@section('title', 'Pesanan ' . $pesanan->kode_transaksi . ' - SMART BOOKS')

@section('content')
<div class="max-w-2xl mx-auto">

    <a href="{{ route('pesanan.index') }}"
       class="inline-flex items-center gap-1 text-sm text-[#004ac6] hover:underline mb-4 no-print">
        ← Semua pesanan
    </a>

    <div class="bg-white rounded-2xl border border-[#c3c6d7] p-6 space-y-5">

        {{-- HEADER PESANAN --}}
        <div class="flex flex-wrap justify-between items-start gap-2 pb-4 border-b">
            <div>
                <h1 class="text-lg font-black">{{ $pesanan->kode_transaksi }}</h1>
                <p class="text-xs text-gray-500">
                    {{ $pesanan->tanggal->format('d M Y H:i') }}
                </p>
            </div>
            @include('pesanan._badge', ['status' => $pesanan->status])
        </div>

        {{-- STATUS PESANAN --}}
        @if($pesanan->status === 'Pending')

            <div class="text-sm text-amber-900 bg-amber-50 border border-amber-200 rounded-xl p-3">
                <b>Menunggu pembayaran.</b>
                Selesaikan pembayaran dalam 10 menit.
                Batas waktu:
                {{ optional($pesanan->expired_at)->format('d M Y H:i') ?? '-' }}.
            </div>

            {{-- TRANSFER BANK --}}
            @if($pesanan->metode_pembayaran === 'Transfer Bank')

                <div class="rounded-xl border p-4 text-sm">
                    <b>Instruksi transfer bank</b>
                    <p>Bank: {{ \App\Models\Setting::value('bank_name', 'Belum diatur toko') }}</p>
                    <p>Nomor rekening: {{ \App\Models\Setting::value('bank_account', 'Belum diatur toko') }}</p>
                    <p>Atas nama: {{ \App\Models\Setting::value('bank_holder', '-') }}</p>
                    <p class="mt-2 text-xs text-gray-500">
                        Cantumkan kode pesanan saat transfer.
                    </p>
                </div>

            {{-- QRIS --}}
            @elseif($pesanan->metode_pembayaran === 'QRIS')

                @php
                    $qrisImagePath = \App\Models\Setting::value('qris_image_path');
                @endphp

                <div class="rounded-xl border p-4 text-sm space-y-3">
                    <b>Instruksi pembayaran QRIS</b>

                    <p class="whitespace-pre-line">
                        {{ \App\Models\Setting::value('qris_info', 'Scan QRIS toko di bawah ini untuk membayar.') }}
                    </p>

                    @if($qrisImagePath)
                        <img
                            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($qrisImagePath) }}"
                            alt="QRIS pembayaran toko"
                            class="mx-auto max-w-xs max-h-80 object-contain rounded-xl border bg-white p-2"
                        >
                    @else
                        <p class="text-amber-700 text-xs">
                            Gambar QRIS belum diunggah oleh admin. Silakan hubungi toko.
                        </p>
                    @endif

                    <p class="text-xs text-gray-500">
                        Pastikan nama merchant benar dan nominal sesuai total pesanan.
                    </p>
                </div>

            {{-- COD --}}
            @else

                <div class="rounded-xl border p-4 text-sm">
                    <b>Pembayaran COD</b>
                    <p>Siapkan pembayaran saat pesanan diterima.</p>
                </div>

            @endif

            {{-- UPLOAD BUKTI PEMBAYARAN --}}
            @if(in_array($pesanan->metode_pembayaran, ['Transfer Bank', 'QRIS'], true))

                <div class="rounded-xl border p-4 space-y-3 text-sm">

                    <div>
                        <b>Unggah bukti pembayaran</b>
                        <p class="text-xs text-gray-500 mt-1">
                            Kirim screenshot/foto bukti transfer atau pembayaran QRIS.
                            Format JPG, PNG, WEBP, maksimal 5 MB.
                        </p>
                    </div>

                    @if($pesanan->payment_proof_path)
                        <div class="rounded-lg bg-green-50 border border-green-200 p-3 text-xs text-green-800">
                            Bukti pembayaran sudah diunggah.
                            Toko perlu memeriksanya sebelum pesanan diproses.

                            <a
                                href="{{ route('pesanan.proof', $pesanan) }}"
                                target="_blank"
                                rel="noopener"
                                class="block mt-1 font-bold underline"
                            >
                                Lihat bukti yang diunggah
                            </a>
                        </div>
                    @endif

                    <form
                        action="{{ route('pesanan.proof.upload', $pesanan) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="space-y-2"
                    >
                        @csrf

                        <input
                            type="file"
                            name="payment_proof"
                            accept="image/png,image/jpeg,image/webp"
                            required
                            class="block w-full text-xs border rounded-lg p-2"
                        >

                        @error('payment_proof')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        <button
                            type="submit"
                            class="px-4 py-2 rounded-full bg-[#004ac6] text-white text-xs font-bold"
                        >
                            {{ $pesanan->payment_proof_path ? 'Ganti bukti pembayaran' : 'Upload bukti pembayaran' }}
                        </button>
                    </form>
                </div>

            @endif

        @elseif($pesanan->status === 'Cancelled')

            <p class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl p-3">
                Pesanan ini dibatalkan. Stok telah dikembalikan.
            </p>

        @else

            <p class="text-sm text-blue-800 bg-blue-50 border border-blue-200 rounded-xl p-3">
                Status pesanan: {{ $pesanan->status }}.
                Terima kasih telah berbelanja!
            </p>

            {{-- BUKTI PENERIMAAN --}}
            @if($pesanan->status === 'Completed' && $pesanan->bukti_penerimaan_path)

                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 space-y-2">
                    <div class="flex items-center gap-2 text-emerald-800">
                        <span class="material-symbols-outlined">verified</span>
                        <b class="text-sm">Bukti pesanan diterima</b>
                    </div>

                    <p class="text-xs text-emerald-800">
                        Kurir mengunggah foto bukti penerimaan pada
                        {{ $pesanan->bukti_penerimaan_uploaded_at?->format('d M Y, H:i') ?? '-' }}.
                    </p>

                    <a
                        href="{{ route('pesanan.delivery-proof', $pesanan) }}"
                        target="_blank"
                        rel="noopener"
                        class="inline-flex items-center gap-1 rounded-lg bg-emerald-700 px-3 py-2 text-xs font-bold text-white"
                    >
                        <span class="material-symbols-outlined text-sm">image</span>
                        Lihat foto bukti penerimaan
                    </a>
                </div>

            @endif
        @endif

        {{-- PROGRESS PESANAN --}}
        @if($pesanan->status !== 'Cancelled')

            @php
                $steps = [
                    'Pending' => 'Pesanan dibuat',
                    'Diproses' => 'Diproses',
                    'Dikirim' => 'Dikirim',
                    'Completed' => 'Selesai',
                ];

                $statuses = array_keys($steps);
                $currentStep = array_search($pesanan->status, $statuses, true);
            @endphp

            <div class="grid grid-cols-4 gap-2 text-center text-[10px] font-semibold">
                @foreach($steps as $state => $label)

                    @php
                        $stepIndex = array_search($state, $statuses, true);
                        $active = $currentStep !== false && $currentStep >= $stepIndex;
                    @endphp

                    <div class="rounded-lg p-2 {{ $active ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-400' }}">
                        {{ $label }}
                    </div>

                @endforeach
            </div>

        @endif

        {{-- NOMOR RESI --}}
        @if($pesanan->nomor_resi)
            <p class="text-sm">
                <b>Nomor resi:</b> {{ $pesanan->nomor_resi }}
            </p>
        @endif

        {{-- DAFTAR BUKU --}}
        <div class="divide-y text-sm">
            @foreach($pesanan->details as $d)

                <div class="py-3 flex justify-between gap-3">
                    <div>
                        <p class="font-semibold">
                            {{ $d->buku->judul ?? 'Buku' }}
                        </p>

                        <p class="text-xs text-gray-500">
                            {{ $d->jumlah }} × @rupiah($d->harga_satuan)
                        </p>

                        @if($d->buku)
                            <form
                                action="{{ route('keranjang.add', $d->buku) }}"
                                method="POST"
                                class="mt-1 no-print"
                            >
                                @csrf
                                <input type="hidden" name="qty" value="{{ $d->jumlah }}">

                                <button class="text-xs text-blue-700 font-semibold hover:underline">
                                    Beli lagi
                                </button>
                            </form>
                        @endif
                    </div>

                    <span class="font-semibold">
                        @rupiah($d->subtotal)
                    </span>
                </div>

            @endforeach
        </div>

        {{-- RINCIAN PEMBAYARAN --}}
        <div class="space-y-1.5 text-sm border-t pt-4">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span>@rupiah($pesanan->subtotal)</span>
            </div>

            <div class="flex justify-between">
                <span>PPN</span>
                <span>@rupiah($pesanan->ppn)</span>
            </div>

            <div class="flex justify-between">
                <span>Ongkos kirim</span>
                <span>@rupiah($pesanan->ongkir)</span>
            </div>

            <div class="flex justify-between font-bold text-lg text-[#004ac6] border-t pt-2">
                <span>Total</span>
                <span>@rupiah($pesanan->total_bayar)</span>
            </div>
        </div>

        {{-- INFORMASI PENERIMA --}}
        <div class="border-t pt-4 text-sm space-y-2">

            <div>
                <p class="text-xs text-gray-500">Penerima</p>
                <p>
                    {{ $pesanan->nama_penerima }} — {{ $pesanan->telepon_penerima }}
                </p>
            </div>

            <div>
                <p class="text-xs text-gray-500">Alamat pengiriman</p>
                <p class="whitespace-pre-line">
                    {{ $pesanan->alamat_pengiriman }}
                </p>

                @if($pesanan->latitude !== null && $pesanan->longitude !== null)
                    <p class="mt-1 text-xs text-emerald-700 font-semibold">
                        Titik lokasi GPS sudah dibagikan.
                    </p>

                    <a
                        target="_blank"
                        rel="noopener"
                        href="https://www.google.com/maps?q={{ urlencode($pesanan->latitude . ',' . $pesanan->longitude) }}"
                        class="text-xs text-blue-700 font-bold underline"
                    >
                        Lihat titik lokasi di Google Maps
                    </a>
                @endif
            </div>

            <div>
                <p class="text-xs text-gray-500">Metode pembayaran</p>
                <p>{{ $pesanan->metode_pembayaran }}</p>
            </div>

            @if($pesanan->catatan)
                <div>
                    <p class="text-xs text-gray-500">Catatan</p>
                    <p>{{ $pesanan->catatan }}</p>
                </div>
            @endif

        </div>

        {{-- WHATSAPP --}}
        @php
            $wa = preg_replace(
                '/[^0-9]/',
                '',
                (string) \App\Models\Setting::value('whatsapp', '')
            );

            if (str_starts_with($wa, '0')) {
                $wa = '62' . substr($wa, 1);
            }

            $message = 'Halo SMART BOOKS, saya ingin konfirmasi pesanan '
                . $pesanan->kode_transaksi
                . ' atas nama '
                . $pesanan->nama_penerima
                . '.';
        @endphp

        {{-- TOMBOL AKSI --}}
        <div class="flex flex-wrap justify-between items-center gap-3 pt-2 no-print">

            <button
                type="button"
                onclick="window.print()"
                class="text-xs font-bold text-gray-600"
            >
                Cetak
            </button>

            <div class="flex gap-2">

                @if($wa)
                    <a
                        target="_blank"
                        rel="noopener"
                        href="https://wa.me/{{ $wa }}?text={{ rawurlencode($message) }}"
                        class="px-4 py-2 rounded-full bg-green-600 text-white text-xs font-bold"
                    >
                        Konfirmasi via WhatsApp
                    </a>
                @endif

                @if($pesanan->status === 'Pending')
                    <form
                        action="{{ route('pesanan.cancel', $pesanan) }}"
                        method="POST"
                        onsubmit="return confirm('Batalkan pesanan ini?')"
                    >
                        @csrf

                        <button class="px-4 py-2 rounded-full border border-red-600 text-red-600 text-xs font-bold">
                            Batalkan
                        </button>
                    </form>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection
