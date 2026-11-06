<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosKasirController extends Controller
{
    public function index(Request $request)
    {
        $query = Buku::where('status', 'publikasi');

        if ($request->filled('kategori') && $request->kategori !== 'Semua') {
            $query->where('kategori', 'like', '%' . $request->kategori . '%');
        }

        if ($request->filled('q')) {
            $search = $request->q;

            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        $books = $query->orderBy('judul', 'asc')->get();

        $categories = collect(['Semua'])->merge(
            Buku::where('status', 'publikasi')
                ->distinct()
                ->orderBy('kategori')
                ->pluck('kategori')
        )->values();

        $trxCode = 'TRX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        $booksJson = $books->map(function ($b) {
            return [
                'id' => $b->id,
                'judul' => $b->judul,
                'penulis' => $b->penulis,
                'kategori' => $b->kategori,
                'isbn' => $b->isbn,
                'harga' => (int) $b->harga_net,
                'stok' => (int) $b->stok,
                'cover' => $b->cover_url,
            ];
        })->values();

        return view('pos.index', compact(
            'books',
            'categories',
            'trxCode',
            'booksJson'
        ));
    }

    public function process(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.buku_id' => 'required|exists:bukus,id',
            'items.*.qty' => 'required|integer|min:1',
            'metode_pembayaran' => 'required|in:Tunai,Kartu,QRIS',
            'bayar' => 'nullable|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {

            $subtotal = 0;
            $itemsData = [];

            foreach ($request->items as $item) {

                // Kunci data buku agar stok aman dari transaksi bersamaan.
                $buku = Buku::whereKey($item['buku_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($buku->status !== 'publikasi') {
                    return back()->with('error', "Buku '{$buku->judul}' tidak tersedia.");
                }

                if ($buku->stok < $item['qty']) {
                    return back()->with('error',
                        "Stok untuk buku '{$buku->judul}' tidak mencukupi (Sisa {$buku->stok})."
                    );
                }

                $harga = (int) $buku->harga_net;
                $qty = (int) $item['qty'];
                $lineSubtotal = $harga * $qty;

                $subtotal += $lineSubtotal;

                $itemsData[] = [
                    'buku' => $buku,
                    'qty' => $qty,
                    'harga' => $harga,
                    'subtotal' => $lineSubtotal,
                ];
            }

            $diskon = (int) ($request->diskon ?? 0);
            if ($diskon > $subtotal) {
                return back()->withInput()->with('error', 'Diskon tidak boleh melebihi subtotal barang.');
            }
            $diskon = max(0, $diskon);
            $ppn = (int) round(($subtotal - $diskon) * 0.11);
            $totalBayar = $subtotal - $diskon + $ppn;

            $metode = $request->metode_pembayaran;

            $bayar = $metode === 'Tunai'
                ? (int) ($request->bayar ?? 0)
                : $totalBayar;

            if ($metode === 'Tunai' && $bayar < $totalBayar) {
                return back()->withInput()->with(
                    'error',
                    'Uang pembayaran tidak mencukupi.'
                );
            }

            $kembali = max(0, $bayar - $totalBayar);

            $kodeTransaksi = 'TRX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $penjualan = Penjualan::create([
                'kode_transaksi' => $kodeTransaksi,
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'ppn' => $ppn,
                'diskon' => $diskon,
                'total_bayar' => $totalBayar,
                'metode_pembayaran' => $metode,
                'bayar' => $bayar,
                'kembali' => $kembali,
                'status' => 'Completed',
                'tanggal' => now(),
            ]);

            // Simpan detail transaksi dan kurangi stok.
            foreach ($itemsData as $row) {

                DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'buku_id' => $row['buku']->id,
                    'jumlah' => $row['qty'],
                    'harga_satuan' => $row['harga'],
                    'harga_pokok_satuan' => (int) $row['buku']->harga_pokok,
                    'subtotal' => $row['subtotal'],
                ]);

                $row['buku']->decrement('stok', $row['qty']);
            }

            // Data struk harus berupa array sederhana,
            // bukan objek model Buku.
            $receiptItems = [];

            foreach ($itemsData as $row) {
                $receiptItems[] = [
                    'judul' => $row['buku']->judul,
                    'qty' => $row['qty'],
                    'harga' => $row['harga'],
                    'subtotal' => $row['subtotal'],
                ];
            }

            return redirect()
                ->route('pos.index')
                ->with('success_trx', [
                    'kode' => $kodeTransaksi,
                    'total' => $totalBayar,
                    'bayar' => $bayar,
                    'kembali' => $kembali,
                    'metode' => $metode,
                    'items' => $receiptItems,
                ]);
        });
    }
}
