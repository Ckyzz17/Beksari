<?php

namespace App\Support;

use App\Models\Buku;

/**
 * Keranjang belanja pelanggan, disimpan di session sebagai [buku_id => qty].
 */
class Cart
{
    public const PPN_RATE = 0.11;

    public static function raw(): array
    {
        return session('cart', []);
    }

    public static function count(): int
    {
        return (int) array_sum(self::raw());
    }

    /**
     * Daftar baris keranjang. Item yang bukunya hilang/habis dibuang
     * dan jumlah dipotong sesuai stok terbaru (session ikut dirapikan).
     */
    public static function lines(): array
    {
        $cart = self::raw();
        if (! $cart) {
            return [];
        }

        $bukus = Buku::whereIn('id', array_keys($cart))
            ->where('status', 'publikasi')
            ->get()
            ->keyBy('id');

        $lines = [];
        $clean = [];

        foreach ($cart as $id => $qty) {
            $buku = $bukus->get($id);
            if (! $buku || $buku->stok < 1) {
                continue;
            }

            $qty = min((int) $qty, $buku->stok);
            if ($qty < 1) {
                continue;
            }

            $clean[$id] = $qty;
            $lines[] = [
                'buku' => $buku,
                'qty' => $qty,
                'harga' => (int) $buku->harga_net,
                'subtotal' => (int) $buku->harga_net * $qty,
            ];
        }

        if ($clean !== $cart) {
            session(['cart' => $clean]);
        }

        return $lines;
    }

    public static function totals(array $lines): array
    {
        $subtotal = (int) collect($lines)->sum('subtotal');
        $ppn = (int) round($subtotal * self::PPN_RATE);

        return [
            'subtotal' => $subtotal,
            'ppn' => $ppn,
            'total' => $subtotal + $ppn,
        ];
    }

    public static function clear(): void
    {
        session()->forget('cart');
    }
}
