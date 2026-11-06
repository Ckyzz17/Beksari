@extends('layouts.app')

@section('title', 'SMART BOOKS - POS Kasir')
@section('header_title', 'Point of Sale (POS) Kasir')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
<script>
    window.POS_BOOKS = @json($booksJson);
    window.POS_CATS = @json($categories);

    function pos() {
        return {
            books: window.POS_BOOKS,
            cats: window.POS_CATS,
            q: '',
            cat: 'Semua',
            cart: [],
            diskon: 0,
            metode: 'Tunai',
            bayar: '',
            submitting: false,
            confirming: false,
            paid: false,

            get filtered() {
                const q = this.q.trim().toLowerCase();
                return this.books.filter(b =>
                    (this.cat === 'Semua' || b.kategori === this.cat) &&
                    (!q || b.judul.toLowerCase().includes(q) || (b.isbn || '').toLowerCase().includes(q) || (b.penulis || '').toLowerCase().includes(q))
                );
            },
            get subtotal() { return this.cart.reduce((s, i) => s + i.harga * i.qty, 0); },
            get ppn() { return Math.round(this.subtotal * 0.11); },
            get disc() { return Math.min(Math.max(parseInt(this.diskon) || 0, 0), this.subtotal + this.ppn); },
            get total() { return this.subtotal + this.ppn - this.disc; },
            get bayarNum() { return this.metode === 'Tunai' ? (parseInt(this.bayar) || 0) : this.total; },
            get kembali() { return Math.max(0, this.bayarNum - this.total); },
            get canPay() {
                return this.cart.length > 0 && !this.submitting && (this.metode !== 'Tunai' || this.bayarNum >= this.total);
            },

            fmt(n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(n); },
            sisa(b) {
                const it = this.cart.find(i => i.id === b.id);
                return b.stok - (it ? it.qty : 0);
            },
            add(b) {
                const it = this.cart.find(i => i.id === b.id);
                if (it) { if (it.qty < b.stok) it.qty++; }
                else if (b.stok > 0) this.cart.push({ id: b.id, judul: b.judul, harga: b.harga, stok: b.stok, qty: 1 });
            },
            inc(i) { if (i.qty < i.stok) i.qty++; },
            dec(i) { if (i.qty > 1) i.qty--; else this.remove(i); },
            remove(i) { this.cart = this.cart.filter(x => x.id !== i.id); },
            reset() { this.cart = []; this.diskon = 0; this.bayar = ''; this.metode = 'Tunai'; },

            // Tunai langsung diproses. Kartu/QRIS harus dikonfirmasi dulu oleh kasir.
            onSubmit(e) {
                if (this.metode !== 'Tunai' && !this.paid) {
                    e.preventDefault();
                    this.confirming = true;
                    return;
                }
                this.submitting = true;
            },
            confirmPaid() {
                this.paid = true;
                this.confirming = false;
                this.$nextTick(() => this.$refs.form.requestSubmit());
            },
            cancelConfirm() {
                this.confirming = false;
                this.paid = false;
            },
        };
    }
</script>
<style>
    @media print {
        body * { visibility: hidden; }
        #struk, #struk * { visibility: visible; }
        #struk { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none; }
        .no-print { display: none !important; }
    }
</style>

<div x-data="pos()" class="flex flex-col lg:flex-row gap-6 lg:h-[calc(100vh-150px)]">
    {{-- Konfirmasi pembayaran non-tunai --}}
    <div x-show="confirming" style="display: none;" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4 no-print">
        <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-xl text-center">
            <span class="material-symbols-outlined text-5xl text-[#004ac6]" x-text="metode === 'QRIS' ? 'qr_code_2' : 'credit_card'"></span>
            <h3 class="font-bold text-lg mt-2" x-text="metode === 'QRIS' ? 'Menunggu pembayaran QRIS' : 'Menunggu pembayaran Kartu'"></h3>
            <p class="text-3xl font-extrabold text-[#004ac6] my-3" x-text="fmt(total)"></p>
            <p class="text-xs text-gray-600 mb-5" x-show="metode === 'QRIS'">Minta pelanggan memindai QRIS toko, lalu pastikan pembayarannya sudah masuk sebelum melanjutkan.</p>
            <p class="text-xs text-gray-600 mb-5" x-show="metode === 'Kartu'">Gesek atau tempel kartu pelanggan di mesin EDC, lalu pastikan transaksinya disetujui sebelum melanjutkan.</p>
            <div class="flex gap-2">
                <button type="button" x-on:click="cancelConfirm()" class="flex-1 py-3 rounded-full border border-[#c3c6d7] text-sm font-bold hover:bg-gray-50">Batal</button>
                <button type="button" x-on:click="confirmPaid()" class="flex-1 py-3 rounded-full bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-700">Pembayaran diterima</button>
            </div>
            <p class="text-[11px] text-gray-400 mt-3">Konfirmasi dilakukan manual oleh kasir. Transaksi baru dicatat setelah tombol hijau ditekan.</p>
        </div>
    </div>

    {{-- Rak buku --}}
    <div class="flex-1 bg-white rounded-2xl border border-[#c3c6d7] p-5 flex flex-col min-h-[420px] overflow-hidden">
        <input type="text" x-model="q" placeholder="Cari judul, penulis, atau ISBN..." class="w-full rounded-full border-gray-300 text-sm px-4 py-2 mb-3">

        <div class="flex gap-2 overflow-x-auto pb-3 mb-3 border-b">
            <template x-for="c in cats" :key="c">
                <button type="button" x-on:click="cat = c"
                    :class="cat === c ? 'bg-[#004ac6] text-white' : 'border border-gray-300 text-gray-600 hover:bg-gray-100'"
                    class="px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap" x-text="c"></button>
            </template>
        </div>

        <div class="flex-1 overflow-y-auto grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 pr-1 content-start">
            <template x-for="b in filtered" :key="b.id">
                <button type="button" x-on:click="add(b)" :disabled="sisa(b) <= 0"
                    class="text-left border rounded-xl p-3 flex flex-col hover:shadow-md transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <div class="aspect-[3/4] bg-gray-100 rounded-lg overflow-hidden relative mb-2 w-full">
                        <img :src="b.cover" :alt="b.judul" class="w-full h-full object-cover" onerror="this.style.display='none'">
                        <span class="absolute top-2 right-2 bg-white/90 text-[11px] font-bold px-2 py-0.5 rounded shadow"
                              :class="sisa(b) <= 5 ? 'text-red-600' : ''" x-text="sisa(b) > 0 ? 'Sisa: ' + sisa(b) : 'Habis'"></span>
                    </div>
                    <h4 class="font-bold text-xs line-clamp-2" x-text="b.judul"></h4>
                    <p class="text-[11px] text-gray-500 line-clamp-1" x-text="b.penulis"></p>
                    <span class="font-bold text-sm text-[#004ac6] mt-2" x-text="fmt(b.harga)"></span>
                </button>
            </template>
            <p x-show="filtered.length === 0" class="col-span-full text-center text-sm text-gray-400 py-10">Tidak ada buku yang cocok.</p>
        </div>
    </div>

    {{-- Keranjang --}}
    <div class="w-full lg:w-[400px] bg-white rounded-2xl border border-[#c3c6d7] p-5 flex flex-col">
        <div class="flex justify-between items-start mb-3">
            <div>
                <h3 class="font-bold text-lg">Keranjang Kasir</h3>
                <p class="text-xs text-gray-500">{{ $trxCode }}</p>
            </div>
            <button type="button" x-show="cart.length" x-on:click="reset()" class="text-xs text-[#ba1a1a] hover:underline">Kosongkan</button>
        </div>

        <form action="{{ route('pos.process') }}" method="POST" x-ref="form" x-on:submit="onSubmit($event)" class="flex-1 flex flex-col min-h-0">
            @csrf
            <template x-for="(i, idx) in cart" :key="'h' + i.id">
                <div>
                    <input type="hidden" :name="'items[' + idx + '][buku_id]'" :value="i.id">
                    <input type="hidden" :name="'items[' + idx + '][qty]'" :value="i.qty">
                </div>
            </template>
            <input type="hidden" name="diskon" :value="disc">
            <input type="hidden" name="bayar" :value="bayarNum">

            <div class="flex-1 overflow-y-auto space-y-3 mb-4 min-h-[120px]">
                <p x-show="cart.length === 0" class="text-xs text-gray-400 text-center py-6">Klik buku di rak untuk menambahkannya ke keranjang.</p>
                <template x-for="i in cart" :key="i.id">
                    <div class="flex items-center gap-3 border-b pb-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold line-clamp-1" x-text="i.judul"></p>
                            <p class="text-[11px] text-gray-500" x-text="fmt(i.harga)"></p>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" x-on:click="dec(i)" class="w-6 h-6 rounded-full bg-gray-100 hover:bg-gray-200 text-sm">&minus;</button>
                            <span class="w-6 text-center text-xs font-bold" x-text="i.qty"></span>
                            <button type="button" x-on:click="inc(i)" :disabled="i.qty >= i.stok" class="w-6 h-6 rounded-full bg-blue-100 text-[#004ac6] hover:bg-blue-200 text-sm disabled:opacity-40">+</button>
                        </div>
                        <p class="text-xs font-bold w-24 text-right" x-text="fmt(i.harga * i.qty)"></p>
                    </div>
                </template>
            </div>

            <div class="border-t pt-4 space-y-2 text-sm">
                <div class="flex justify-between text-gray-600"><span>Subtotal</span><span x-text="fmt(subtotal)"></span></div>
                <div class="flex justify-between text-gray-600"><span>PPN (11%)</span><span x-text="fmt(ppn)"></span></div>
                <div class="flex justify-between items-center text-gray-600">
                    <span>Diskon (Rp)</span>
                    <input type="number" min="0" x-model="diskon" class="w-28 text-right rounded-lg border-gray-300 text-xs py-1">
                </div>
                <div class="flex justify-between font-bold text-lg pt-2 border-t text-[#004ac6]">
                    <span>Total Bayar</span><span x-text="fmt(total)"></span>
                </div>

                <div class="grid grid-cols-3 gap-2 pt-2">
                    @foreach(['Tunai', 'Kartu', 'QRIS'] as $m)
                    <label class="cursor-pointer">
                        <input type="radio" name="metode_pembayaran" value="{{ $m }}" x-model="metode" class="peer sr-only">
                        <span class="block text-center text-xs font-semibold py-2 rounded-lg border border-gray-300 peer-checked:bg-[#004ac6] peer-checked:text-white peer-checked:border-[#004ac6]">{{ $m }}</span>
                    </label>
                    @endforeach
                </div>

                <div x-show="metode === 'Tunai'" class="space-y-2 pt-1">
                    <div class="flex justify-between items-center text-gray-600">
                        <span>Uang diterima</span>
                        <input type="number" min="0" x-model="bayar" placeholder="0" class="w-32 text-right rounded-lg border-gray-300 text-xs py-1">
                    </div>
                    <div class="flex justify-between text-gray-600"><span>Kembalian</span><span class="font-bold" x-text="fmt(kembali)"></span></div>
                    <p x-show="bayar !== '' && bayarNum < total" class="text-[11px] text-red-600">Uang diterima masih kurang dari total.</p>
                </div>

                <button type="submit" :disabled="!canPay"
                    class="w-full py-4 bg-[#004ac6] text-white font-bold rounded-xl mt-3 hover:bg-blue-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                    <span x-text="metode === 'Tunai' ? 'Proses Transaksi' : 'Lanjut ke Pembayaran'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Struk setelah transaksi berhasil --}}
@if(session('success_trx'))
@php($trx = session('success_trx'))
<div x-data="{ open: true }" x-show="open" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div id="struk" class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-xl max-h-[90vh] overflow-y-auto">
        <div class="text-center mb-4">
            <h3 class="font-black text-[#004ac6] text-lg">SMART BOOKS</h3>
            <p class="text-[11px] text-gray-500">{{ $trx['kode'] }} &bull; {{ now()->format('d M Y H:i') }}</p>
        </div>
        <div class="text-xs divide-y border-y">
            @foreach($trx['items'] as $row)
            <div class="py-2 flex justify-between gap-3">
                <div>
                    <p class="font-semibold">{{ $row['judul'] }}</p>
                    <p class="text-gray-500">{{ $row['qty'] }} &times; @rupiah($row['harga'])</p>
                </div>
                <p class="font-semibold">@rupiah($row['subtotal'])</p>
            </div>
            @endforeach
        </div>
        <div class="text-xs space-y-1 mt-3">
            <div class="flex justify-between"><span>Metode</span><span>{{ $trx['metode'] }}</span></div>
            <div class="flex justify-between font-bold text-sm"><span>Total</span><span>@rupiah($trx['total'])</span></div>
            <div class="flex justify-between"><span>Dibayar</span><span>@rupiah($trx['bayar'])</span></div>
            <div class="flex justify-between"><span>Kembalian</span><span>@rupiah($trx['kembali'])</span></div>
        </div>
        <p class="text-center text-[11px] text-gray-500 mt-4">Terima kasih telah berbelanja buku di SMART BOOKS!</p>
        <div class="flex gap-2 mt-5 no-print">
            <button type="button" onclick="window.print()" class="flex-1 py-2.5 rounded-full border border-[#c3c6d7] text-xs font-bold hover:bg-gray-50">Cetak struk</button>
            <button type="button" x-on:click="open = false" class="flex-1 py-2.5 rounded-full bg-[#004ac6] text-white text-xs font-bold hover:bg-blue-700">Transaksi baru</button>
        </div>
    </div>
</div>
@endif
@endsection