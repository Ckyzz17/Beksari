@extends('layouts.shop')

@section('title', 'Checkout - SMART BOOKS')

@section('content')
<div class="mb-6">
    <p class="text-sm font-semibold text-[#2563eb]">Satu langkah lagi</p>
    <h1 class="text-2xl font-black text-gray-900">Checkout</h1>
    <p class="mt-1 text-sm text-gray-500">Pastikan data penerima dan titik pengiriman sudah benar.</p>
</div>

<form action="{{ route('checkout.store') }}" method="POST" onsubmit="const b=this.querySelector('[type=submit]'); if(b){b.disabled=true;b.textContent='Memproses...'}" class="flex flex-col lg:flex-row gap-6 items-start">
    @csrf
    <input type="hidden" name="checkout_token" value="{{ $checkoutToken }}">
    <div class="flex-1 w-full space-y-5">
        <section class="bg-white rounded-2xl border border-[#d9deea] p-5 md:p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center"><span class="material-symbols-outlined">location_on</span></div>
                <div><h2 class="font-bold text-gray-900">Data penerima & alamat pengiriman</h2><p class="text-xs text-gray-500 mt-0.5">Informasi ini digunakan kurir untuk mengantar pesanan.</p></div>
            </div>

            <label class="block text-sm font-semibold mb-1.5" for="nama_penerima">Nama penerima</label>
            <input id="nama_penerima" name="nama_penerima" required maxlength="255" value="{{ old('nama_penerima', $user->name) }}" class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 mb-4">

            <label class="block text-sm font-semibold mb-1.5" for="telepon_penerima">Nomor WhatsApp / telepon</label>
            <input id="telepon_penerima" name="telepon_penerima" required maxlength="30" value="{{ old('telepon_penerima', $user->telepon) }}" class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 mb-4">

            <label class="block text-sm font-semibold mb-1.5" for="alamat_pengiriman">Alamat lengkap</label>
            <textarea id="alamat_pengiriman" name="alamat_pengiriman" rows="3" required placeholder="Jalan, nomor rumah, desa/kelurahan, kecamatan, kota, kode pos" class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('alamat_pengiriman', $user->alamat) }}</textarea>
            @error('alamat_pengiriman')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror

            <div class="mt-5 rounded-2xl border border-blue-100 bg-blue-50/70 p-4 md:p-5">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 shrink-0 rounded-xl bg-white text-blue-700 flex items-center justify-center shadow-sm"><span class="material-symbols-outlined">my_location</span></div>
                    <div class="min-w-0 flex-1">
                        <h3 class="font-bold text-gray-900">Titik lokasi rumah</h3>
                        <p class="text-xs text-gray-600 mt-1 leading-relaxed">Bagikan lokasi GPS agar kurir lebih mudah menemukan alamatmu. Fitur ini opsional; alamat lengkap tetap harus diisi.</p>
                    </div>
                </div>
                <input type="hidden" name="latitude" id="delivery-latitude" value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="delivery-longitude" value="{{ old('longitude') }}">
                <div class="mt-4 flex flex-col sm:flex-row sm:items-center gap-3">
                    <button type="button" id="get-delivery-location" class="inline-flex justify-center items-center gap-2 rounded-xl bg-blue-700 px-4 py-3 text-sm font-bold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <span class="material-symbols-outlined text-lg">my_location</span>
                        <span id="location-button-label">Gunakan Lokasi Saya</span>
                    </button>
                    <p id="delivery-location-status" role="status" aria-live="polite" class="text-xs text-gray-600">Belum ada titik lokasi yang dipilih.</p>
                </div>
                <a id="delivery-location-map" href="#" target="_blank" rel="noopener" class="{{ old('latitude') && old('longitude') ? '' : 'hidden' }} mt-3 inline-flex items-center gap-1 text-xs font-bold text-blue-800 underline">
                    <span class="material-symbols-outlined text-sm">map</span> Periksa titik di Google Maps
                </a>
                @error('latitude')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
                @error('longitude')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <label class="block text-sm font-semibold mt-5 mb-1.5" for="catatan">Catatan untuk toko <span class="font-normal text-gray-400">(opsional)</span></label>
            <input id="catatan" type="text" name="catatan" value="{{ old('catatan') }}" maxlength="500" placeholder="Contoh: rumah pagar hijau, titip ke satpam" class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
        </section>

        <section class="bg-white rounded-2xl border border-[#d9deea] p-5 md:p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center"><span class="material-symbols-outlined">payments</span></div>
                <div><h2 class="font-bold text-gray-900">Metode pembayaran</h2><p class="text-xs text-gray-500 mt-0.5">Pilih cara pembayaran pesananmu.</p></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @foreach($metode as $i => $m)
                <label class="cursor-pointer">
                    <input type="radio" name="metode_pembayaran" value="{{ $m }}" class="peer sr-only" @checked(old('metode_pembayaran', $metode[0]) === $m)>
                    <span class="flex items-center justify-center gap-2 text-sm font-bold py-3.5 rounded-xl border border-gray-300 text-gray-700 peer-checked:bg-[#004ac6] peer-checked:text-white peer-checked:border-[#004ac6] peer-focus-visible:ring-2 peer-focus-visible:ring-blue-500 transition">
                        @if($m === 'Transfer Bank')<span class="material-symbols-outlined text-lg">account_balance</span>@elseif($m === 'QRIS')<span class="material-symbols-outlined text-lg">qr_code_2</span>@else<span class="material-symbols-outlined text-lg">local_shipping</span>@endif
                        {{ $m }}
                    </span>
                </label>
                @endforeach
            </div>
            @error('metode_pembayaran')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
            <p class="text-xs text-gray-500 mt-3">Untuk Transfer Bank dan QRIS, bukti pembayaran dapat diunggah setelah pesanan dibuat.</p>
        </section>
    </div>

    <aside class="w-full lg:w-96 bg-white rounded-2xl border border-[#d9deea] p-5 shadow-sm text-sm lg:sticky lg:top-24">
        <h2 class="font-bold text-gray-900 mb-3">Ringkasan pesanan</h2>
        <div class="divide-y text-xs mb-3">
            @foreach($lines as $line)
            <div class="py-3 flex justify-between gap-3">
                <div class="min-w-0"><p class="font-semibold text-gray-900 line-clamp-1">{{ $line['buku']->judul }}</p><p class="text-gray-500 mt-1">{{ $line['qty'] }} &times; @rupiah($line['harga'])</p></div>
                <span class="font-semibold whitespace-nowrap">@rupiah($line['subtotal'])</span>
            </div>
            @endforeach
        </div>
        <div class="space-y-2 pt-2 border-t">
            <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>@rupiah($totals['subtotal'])</span></div>
            <div class="flex justify-between text-gray-600"><span>PPN (11%)</span><span>@rupiah($totals['ppn'])</span></div>
            <div class="flex justify-between text-gray-600"><span>Ongkos kirim</span><span>@rupiah($totals['ongkir'])</span></div>
            <div class="flex justify-between font-black text-lg text-[#004ac6] pt-3 border-t"><span>Total</span><span>@rupiah($totals['total'])</span></div>
        </div>
        <button type="submit" class="w-full mt-5 py-3.5 rounded-xl bg-[#2563eb] text-white font-bold hover:bg-blue-700 disabled:opacity-60">Buat pesanan</button>
        <a href="{{ route('keranjang.index') }}" class="block text-center text-xs text-[#004ac6] hover:underline mt-3">Kembali ke keranjang</a>
    </aside>
</form>

<script>
(() => {
    const button = document.getElementById('get-delivery-location');
    if (!button) return;

    const latitudeInput = document.getElementById('delivery-latitude');
    const longitudeInput = document.getElementById('delivery-longitude');
    const status = document.getElementById('delivery-location-status');
    const label = document.getElementById('location-button-label');
    const mapLink = document.getElementById('delivery-location-map');

    const showSavedLocation = () => {
        if (!latitudeInput.value || !longitudeInput.value) return;
        status.textContent = `Lokasi tersimpan: ${Number(latitudeInput.value).toFixed(6)}, ${Number(longitudeInput.value).toFixed(6)}`;
        mapLink.href = `https://www.google.com/maps?q=${encodeURIComponent(latitudeInput.value + ',' + longitudeInput.value)}`;
        mapLink.classList.remove('hidden');
        label.textContent = 'Perbarui Lokasi';
    };
    showSavedLocation();

    button.addEventListener('click', () => {
        if (!navigator.geolocation) {
            status.textContent = 'Browser tidak mendukung GPS. Kamu tetap bisa mengisi alamat lengkap.';
            return;
        }

        button.disabled = true;
        label.textContent = 'Mengambil lokasi...';
        status.textContent = 'Izinkan akses lokasi pada browser/HP kamu.';
        navigator.geolocation.getCurrentPosition((position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            latitudeInput.value = lat.toFixed(7);
            longitudeInput.value = lng.toFixed(7);
            status.textContent = `Lokasi berhasil diambil (akurasi ±${Math.round(position.coords.accuracy)} meter).`;
            mapLink.href = `https://www.google.com/maps?q=${encodeURIComponent(lat + ',' + lng)}`;
            mapLink.classList.remove('hidden');
            label.textContent = 'Perbarui Lokasi';
            button.disabled = false;
        }, (error) => {
            const messages = {
                1: 'Izin lokasi ditolak. Aktifkan izin lokasi di browser untuk mencoba lagi.',
                2: 'Lokasi belum dapat ditemukan. Coba aktifkan GPS dan ulangi.',
                3: 'Waktu mengambil lokasi habis. Silakan coba lagi.'
            };
            status.textContent = messages[error.code] || 'Lokasi gagal diambil. Kamu tetap bisa mengisi alamat lengkap.';
            label.textContent = 'Coba Lagi';
            button.disabled = false;
        }, { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 });
    });
})();
</script>
@endsection
