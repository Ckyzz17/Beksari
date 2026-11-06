@extends('layouts.app')

@section('title', 'SMART BOOKS - Tambah Pasok')
@section('header_title', 'Input Pasok Buku')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <h2 class="text-2xl font-bold text-[#191c1e]">Form Penerimaan Pasok Buku</h2>
    <div class="bg-white p-6 rounded-2xl border border-[#c3c6d7] shadow-sm">
        <form action="{{ route('pasok.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold mb-1">Distributor Pengirim *</label>
                <select name="distributor_id" required class="w-full p-2.5 bg-gray-50 border rounded-xl">
                    @foreach($distributors as $d)
                        <option value="{{ $d->id }}">{{ $d->kode }} - {{ $d->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold mb-1">Pilih Buku *</label>
                <select name="buku_id" required class="w-full p-2.5 bg-gray-50 border rounded-xl">
                    @foreach($bukus as $b)
                        <option value="{{ $b->id }}">{{ $b->kode }} - {{ $b->judul }} (Stok saat ini: {{ $b->stok }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold mb-1">Jumlah Pasokan Masuk *</label>
                <input type="number" name="jumlah" min="1" required value="20" class="w-full p-2.5 bg-gray-50 border rounded-xl font-bold">
            </div>
            <div>
                <label class="block font-bold mb-1">Tanggal Terima *</label>
                <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" class="w-full p-2.5 bg-gray-50 border rounded-xl">
            </div>
            <div class="flex justify-end gap-2 pt-4 border-t">
                <a href="{{ route('pasok.index') }}" class="px-5 py-2 font-bold text-gray-600 rounded-full">Batal</a>
                <button type="submit" class="px-6 py-2 bg-[#004ac6] text-white font-bold rounded-full shadow">Simpan & Tambah Stok</button>
            </div>
        </form>
    </div>
</div>
@endsection
