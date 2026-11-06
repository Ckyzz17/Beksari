<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use App\Models\User;
use Illuminate\Database\Seeder;

class PenjualanSeeder extends Seeder
{
    public function run(): void
    {
        $kasir = User::where('role', 'kasir')->first() ?? User::first();
        $buku1 = Buku::where('kode', 'BK-001')->first();
        $buku6 = Buku::where('kode', 'BK-006')->first();

        if ($kasir && $buku1 && $buku6) {
            $penjualan = Penjualan::create([
                'kode_transaksi' => 'TRX-' . date('Ymd') . '-01',
                'user_id' => $kasir->id,
                'subtotal' => 265000,
                'ppn' => 29150,
                'diskon' => 0,
                'total_bayar' => 294150,
                'metode_pembayaran' => 'Tunai',
                'bayar' => 300000,
                'kembali' => 5850,
                'status' => 'Completed',
                'tanggal' => now(),
            ]);

            DetailPenjualan::create([
                'penjualan_id' => $penjualan->id,
                'buku_id' => $buku6->id,
                'jumlah' => 2,
                'harga_satuan' => 85000,
                'subtotal' => 170000,
            ]);

            DetailPenjualan::create([
                'penjualan_id' => $penjualan->id,
                'buku_id' => $buku1->id,
                'jumlah' => 1,
                'harga_satuan' => 95000,
                'subtotal' => 95000,
            ]);
        }
    }
}
