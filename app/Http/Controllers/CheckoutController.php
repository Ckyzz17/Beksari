<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use App\Models\Setting;
use App\Support\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class CheckoutController extends Controller
{
    public const METODE = ['Transfer Bank', 'QRIS', 'COD'];

    public function index()
    {
        $lines = Cart::lines();
        if (! $lines) return redirect()->route('keranjang.index')->with('error', 'Keranjang masih kosong.');
        $totals = Cart::totals($lines);
        $totals['ongkir'] = (int) Setting::value('shipping_flat', 0);
        $totals['total'] += $totals['ongkir'];
        $metode = self::METODE;
        $user = Auth::user();
        if (! session()->has('checkout_token')) {
            session()->put('checkout_token', bin2hex(random_bytes(32)));
        }
        $checkoutToken = session('checkout_token');
        return view('checkout.index', compact('lines', 'totals', 'metode', 'user', 'checkoutToken'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'checkout_token' => 'required|string|size:64',
            'nama_penerima' => 'required|string|max:255',
            'telepon_penerima' => 'required|string|max:30',
            'alamat_pengiriman' => 'required|string|min:10|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'metode_pembayaran' => 'required|in:' . implode(',', self::METODE),
            'catatan' => 'nullable|string|max:500',
        ]);
        $existingOrder = Penjualan::where('checkout_token', $data['checkout_token'])->first();
        if ($existingOrder) {
            Cart::clear();
            session()->forget('checkout_token');
            return redirect()->route('pesanan.show', $existingOrder)->with('info', 'Pesanan ini sudah pernah dibuat.');
        }

        $cart = Cart::raw();
        if (! $cart) return redirect()->route('keranjang.index')->with('error', 'Keranjang masih kosong.');
        $shipping = (int) Setting::value('shipping_flat', 0);

        try {
            $result = DB::transaction(function () use ($cart, $data, $shipping) {
            $bukus = Buku::whereIn('id', array_keys($cart))->where('status', 'publikasi')->lockForUpdate()->get()->keyBy('id');
            $rows = []; $subtotal = 0;
            foreach ($cart as $id => $qty) {
                $buku = $bukus->get($id);
                if (! $buku || $buku->stok < $qty) return ['error' => "Stok '" . ($buku->judul ?? 'salah satu buku') . "' tidak mencukupi. Silakan periksa keranjang Anda."];
                $harga = (int) $buku->harga_net;
                $line = $harga * (int) $qty;
                $subtotal += $line;
                $rows[] = ['buku' => $buku, 'qty' => (int) $qty, 'harga' => $harga, 'subtotal' => $line];
            }
            $ppn = (int) round($subtotal * Cart::PPN_RATE);
            $total = $subtotal + $ppn + $shipping;
            $penjualan = Penjualan::create([
                'kode_transaksi' => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 5)),
                'checkout_token' => $data['checkout_token'],
                'user_id' => Auth::id(), 'subtotal' => $subtotal, 'ppn' => $ppn, 'ongkir' => $shipping,
                'diskon' => 0, 'total_bayar' => $total, 'metode_pembayaran' => $data['metode_pembayaran'],
                'bayar' => 0, 'kembali' => 0, 'status' => 'Pending', 'tipe' => 'online',
                'nama_penerima' => $data['nama_penerima'], 'telepon_penerima' => $data['telepon_penerima'],
                'alamat_pengiriman' => $data['alamat_pengiriman'],
                'latitude' => $data['latitude'] ?? null, 'longitude' => $data['longitude'] ?? null,
                'catatan' => $data['catatan'] ?? null,
                'tanggal' => now(), 'expired_at' => now()->addHours(24),
            ]);
            foreach ($rows as $row) {
                DetailPenjualan::create(['penjualan_id' => $penjualan->id, 'buku_id' => $row['buku']->id, 'jumlah' => $row['qty'], 'harga_satuan' => $row['harga'], 'harga_pokok_satuan' => (int) $row['buku']->harga_pokok, 'subtotal' => $row['subtotal']]);
                $row['buku']->decrement('stok', $row['qty']);
            }
            return ['penjualan' => $penjualan];
            });
        } catch (QueryException $exception) {
            $existingOrder = Penjualan::where('checkout_token', $data['checkout_token'])->first();
            if (! $existingOrder) {
                throw $exception;
            }
            Cart::clear();
            session()->forget('checkout_token');
            return redirect()->route('pesanan.show', $existingOrder)->with('info', 'Pesanan ini sudah pernah dibuat.');
        }
        if (isset($result['error'])) return redirect()->route('keranjang.index')->with('error', $result['error']);
        Cart::clear();
        session()->forget('checkout_token');
        return redirect()->route('pesanan.show', $result['penjualan'])->with('success', 'Pesanan berhasil dibuat! Ikuti instruksi pembayaran di bawah.');
    }
}
