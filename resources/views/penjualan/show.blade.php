@extends('layouts.app')

@section('title', 'Detail Transaksi - SMART BOOKS')
@section('header_title', 'Detail Transaksi')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white p-6 rounded-2xl border border-[#c3c6d7] shadow-sm space-y-4">
        <div class="border-b pb-4 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-black text-gray-900">{{ $penjualan->kode_transaksi }}</h3>
                <p class="text-xs text-gray-500">{{ $penjualan->tanggal->format('d M Y H:i') }} • {{ $penjualan->isOnline() ? 'Pelanggan' : 'Kasir' }}: {{ $penjualan->user->name ?? '-' }}</p>
            </div>
            @include('laporan._status', ['status' => $penjualan->status])
        </div>

        <div class="divide-y text-xs">
            @foreach($penjualan->details as $item)
            <div class="py-2.5 flex justify-between items-center">
                <div>
                    <p class="font-bold text-gray-900">{{ $item->buku->judul ?? 'Buku' }}</p>
                    <p class="text-gray-400">{{ $item->jumlah }} x @rupiah($item->harga_satuan)</p>
                </div>
                <p class="font-bold text-gray-900">@rupiah($item->subtotal)</p>
            </div>
            @endforeach
        </div>

        <div class="border-t pt-4 space-y-1.5 text-xs">
            <div class="flex justify-between"><span>Subtotal:</span><span>@rupiah($penjualan->subtotal)</span></div>
            <div class="flex justify-between"><span>PPN (11%):</span><span>@rupiah($penjualan->ppn)</span></div>
            <div class="flex justify-between"><span>Ongkir:</span><span>@rupiah($penjualan->ongkir)</span></div>
            <div class="flex justify-between"><span>Diskon:</span><span>- @rupiah($penjualan->diskon)</span></div>
            <div class="flex justify-between font-black text-sm text-[#004ac6] pt-2 border-t">
                <span>Total Bayar:</span><span>@rupiah($penjualan->total_bayar)</span>
            </div>
        </div>

        @if($penjualan->isOnline())
        <div class="border-t pt-4 text-xs space-y-2">
            <div><p class="text-gray-400">Penerima</p><p class="font-semibold">{{ $penjualan->nama_penerima ?? $penjualan->user->name ?? '-' }} — {{ $penjualan->telepon_penerima ?? '-' }}</p></div>
            <div><p class="text-gray-400">Metode pembayaran</p><p class="font-semibold">{{ $penjualan->metode_pembayaran }}</p></div>
            @if($penjualan->nomor_resi)<div><p class="text-gray-400">Nomor resi</p><p class="font-semibold">{{ $penjualan->nomor_resi }}</p></div>@endif
            <div><p class="text-gray-400">Alamat pengiriman</p><p class="font-semibold whitespace-pre-line">{{ $penjualan->alamat_pengiriman }}</p></div>
            @if($penjualan->kurir)
                <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-3"><p class="text-indigo-700">Kurir yang ditugaskan</p><p class="font-bold text-indigo-900">{{ $penjualan->kurir->name }}</p><p class="text-indigo-700">{{ $penjualan->kurir->telepon ?: 'Nomor telepon belum diisi' }}</p></div>
            @endif
            @if($penjualan->catatan)<div><p class="text-gray-400">Catatan pelanggan</p><p class="font-semibold">{{ $penjualan->catatan }}</p></div>@endif
            @if($penjualan->payment_proof_path)
                <div class="rounded-xl border border-green-200 bg-green-50 p-3 space-y-2">
                    <p class="font-bold text-green-800">Bukti pembayaran pelanggan sudah diunggah</p>
                    <a href="{{ route('pesanan-masuk.proof', $penjualan) }}" target="_blank" rel="noopener" class="inline-flex px-4 py-2 rounded-full bg-green-700 text-white font-bold">Lihat bukti pembayaran</a>
                </div>
            @elseif(in_array($penjualan->metode_pembayaran, ['Transfer Bank', 'QRIS'], true))
                <p class="rounded-xl bg-amber-50 border border-amber-200 p-3 text-amber-800">Pelanggan belum mengunggah bukti pembayaran.</p>
            @endif
        </div>
        @endif

        @if($penjualan->isOnline() && request()->routeIs('pesanan-masuk.*') && $penjualan->status === 'Diproses')
            <div class="border-t pt-4 space-y-2 no-print">
                <h4 class="text-xs font-bold text-gray-900">Penugasan kurir</h4>
                @if(($kurirs ?? collect())->isNotEmpty())
                    <form method="POST" action="{{ route('pesanan-masuk.assign-kurir', $penjualan) }}" class="flex flex-col sm:flex-row gap-2">@csrf
                        <select name="kurir_id" required class="flex-1 rounded-xl border-gray-300 text-xs">
                            <option value="">Pilih kurir...</option>
                            @foreach($kurirs as $kurir)
                                <option value="{{ $kurir->id }}" @selected($penjualan->kurir_id === $kurir->id)>{{ $kurir->name }}{{ $kurir->telepon ? ' — '.$kurir->telepon : '' }}</option>
                            @endforeach
                        </select>
                        <button class="rounded-full bg-indigo-600 px-5 py-2 text-xs font-bold text-white">Tugaskan kurir</button>
                    </form>
                @else
                    <p class="rounded-xl bg-amber-50 p-3 text-xs text-amber-800">Belum ada akun kurir. Buat akun dengan role Kurir di menu Data User.</p>
                @endif
            </div>
        @endif

        @if($penjualan->isOnline() && in_array($penjualan->status, ['Pending', 'Diproses', 'Dikirim']))
        <div class="border-t pt-4 space-y-3 no-print">
            <form action="{{ request()->routeIs('pesanan-masuk.*') ? route('pesanan-masuk.status', $penjualan) : route('penjualan.status', $penjualan) }}" method="POST" class="flex flex-wrap gap-2 justify-end">@csrf
                @if($penjualan->status === 'Pending')
                    @if($penjualan->metode_pembayaran === 'COD')
                        <button name="status" value="Diproses" class="px-5 py-2 rounded-full bg-[#004ac6] text-white text-xs font-bold">Setujui pesanan & proses</button>
                    @elseif($penjualan->payment_proof_path)
                        <button name="status" value="Diproses" onclick="return confirm('Periksa bukti dan pastikan pembayaran Transfer Bank/QRIS benar-benar sudah masuk. Lanjutkan?')" class="px-5 py-2 rounded-full bg-[#004ac6] text-white text-xs font-bold">Verifikasi pembayaran & proses</button>
                    @else
                        <p class="w-full text-right text-xs text-amber-700">Menunggu pelanggan mengunggah bukti pembayaran sebelum dapat diproses.</p>
                    @endif
                    <button name="status" value="Cancelled" onclick="return confirm('Batalkan dan kembalikan stok?')" class="px-5 py-2 rounded-full border border-red-600 text-red-600 text-xs font-bold">Batalkan</button>
                @elseif($penjualan->status === 'Diproses' && ! $penjualan->kurir_id)
                    <input name="nomor_resi" placeholder="Nomor resi (opsional)" class="rounded-full border-gray-300 text-xs">
                    <button name="status" value="Dikirim" class="px-5 py-2 rounded-full bg-indigo-600 text-white text-xs font-bold">Tandai dikirim</button>
                @elseif($penjualan->status === 'Dikirim' && ! $penjualan->kurir_id)
                    <button name="status" value="Completed" class="px-5 py-2 rounded-full bg-emerald-600 text-white text-xs font-bold">Tandai selesai</button>
                @endif
            </form>
            @if($penjualan->kurir_id && in_array($penjualan->status, ['Diproses', 'Dikirim'], true))
                <p class="text-right text-xs text-indigo-700">Status pengiriman selanjutnya diperbarui oleh kurir yang ditugaskan.</p>
            @endif
        </div>
        @endif

        <div class="border-t pt-4 flex justify-between">
            <a href="{{ request()->routeIs('pesanan-masuk.*') ? route('pesanan-masuk.index') : route('penjualan.index') }}" class="px-5 py-2 text-xs font-bold text-gray-600 rounded-full hover:bg-gray-100">← Kembali</a>
            <button onclick="window.print()" class="px-6 py-2 bg-[#004ac6] text-white text-xs font-bold rounded-full shadow">Cetak Struk</button>
        </div>
    </div>
</div>
@endsection
