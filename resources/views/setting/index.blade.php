@extends('layouts.app')
@section('title', 'Pengaturan Sistem - SMART BOOKS')
@section('header_title', 'Pengaturan Sistem')
@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-2xl border border-[#c3c6d7] shadow-sm">
    <h3 class="font-bold text-base text-gray-900 mb-4 pb-2 border-b">Pengaturan Toko, Pembayaran & Pengiriman</h3>
    <form action="{{ route('setting.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">@csrf
        @foreach(['store_name'=>'Nama toko','store_address'=>'Alamat toko','store_phone'=>'Telepon toko','whatsapp'=>'Nomor WhatsApp toko','bank_name'=>'Nama bank','bank_account'=>'Nomor rekening','bank_holder'=>'Atas nama rekening','qris_info'=>'Instruksi QRIS'] as $key=>$label)
        <div><label class="block font-bold mb-1">{{ $label }}</label>@if(in_array($key,['store_address','qris_info']))<textarea name="{{ $key }}" rows="2" class="w-full p-2.5 bg-gray-50 border rounded-xl">{{ old($key,$settings[$key]) }}</textarea>@else<input name="{{ $key }}" value="{{ old($key,$settings[$key]) }}" class="w-full p-2.5 bg-gray-50 border rounded-xl">@endif</div>
        @endforeach
        <div class="rounded-xl border border-dashed border-gray-300 p-4 space-y-2">
            <label for="qris_image" class="block font-bold">Gambar QRIS toko</label>
            <p class="text-[11px] text-gray-500">Upload gambar QRIS resmi toko dalam format JPG, PNG, atau WEBP. Maksimal 4 MB.</p>
            @if(!empty($settings['qris_image_path']))
                <div class="space-y-2">
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($settings['qris_image_path']) }}" alt="QRIS toko saat ini" class="max-w-[220px] max-h-64 object-contain rounded-lg border bg-white p-2">
                    <label class="inline-flex items-center gap-2"><input type="checkbox" name="remove_qris_image" value="1"> Hapus gambar QRIS saat ini</label>
                </div>
            @else
                <p class="text-[11px] text-amber-700">Belum ada gambar QRIS yang diunggah.</p>
            @endif
            <input id="qris_image" type="file" name="qris_image" accept="image/png,image/jpeg,image/webp" class="block w-full text-xs">
            @error('qris_image')<p class="text-red-600">{{ $message }}</p>@enderror
        </div>
        <div><label class="block font-bold mb-1">Ongkos kirim flat (Rp)</label><input type="number" min="0" name="shipping_flat" value="{{ old('shipping_flat',$settings['shipping_flat']) }}" required class="w-full p-2.5 bg-gray-50 border rounded-xl"></div>
        <div><label class="block font-bold mb-1">Pesan footer struk</label><input name="receipt_footer" value="{{ old('receipt_footer',$settings['receipt_footer']) }}" class="w-full p-2.5 bg-gray-50 border rounded-xl"></div>
        <div class="flex justify-end pt-3 border-t"><button type="submit" class="px-6 py-2 bg-[#004ac6] text-white font-bold rounded-full shadow">Simpan Pengaturan</button></div>
    </form>
</div>
@endsection
