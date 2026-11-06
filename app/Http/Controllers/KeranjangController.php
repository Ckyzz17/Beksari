<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Support\Cart;
use Illuminate\Http\Request;

class KeranjangController extends Controller
{
    public function index()
    {
        $before = Cart::raw();
        $lines = Cart::lines();

        if (Cart::raw() !== $before) {
            session()->flash('warning', 'Beberapa item di keranjang disesuaikan dengan stok terbaru.');
        }

        $totals = Cart::totals($lines);

        return view('keranjang.index', compact('lines', 'totals'));
    }

    public function add(Request $request, Buku $buku)
    {
        $request->validate(['qty' => 'nullable|integer|min:1|max:99']);

        if ($buku->status !== 'publikasi' || $buku->stok < 1) {
            return back()->with('error', "Stok buku '{$buku->judul}' sedang habis.");
        }

        $qty = (int) $request->input('qty', 1);
        $cart = Cart::raw();
        $wanted = ($cart[$buku->id] ?? 0) + $qty;
        $new = min($wanted, $buku->stok);

        $cart[$buku->id] = $new;
        session(['cart' => $cart]);

        if ($new < $wanted) {
            return back()->with('warning', "Jumlah '{$buku->judul}' disesuaikan dengan stok yang tersedia ({$buku->stok}).");
        }

        return back()->with('success', "'{$buku->judul}' ditambahkan ke keranjang.");
    }

    public function update(Request $request, Buku $buku)
    {
        $data = $request->validate(['qty' => 'required|integer|min:1|max:99']);

        $cart = Cart::raw();
        if (isset($cart[$buku->id])) {
            $cart[$buku->id] = min($data['qty'], max($buku->stok, 1));
            session(['cart' => $cart]);
        }

        return back();
    }

    public function remove(Buku $buku)
    {
        $cart = Cart::raw();
        unset($cart[$buku->id]);
        session(['cart' => $cart]);

        return back()->with('success', 'Buku dihapus dari keranjang.');
    }
}
