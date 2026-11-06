<?php

namespace App\Console\Commands;

use App\Models\Penjualan;
use Illuminate\Console\Command;

class ExpirePendingOrders extends Command
{
    protected $signature = 'orders:expire-pending';
    protected $description = 'Batalkan pesanan pending yang melewati batas pembayaran dan kembalikan stok.';

    public function handle(): int
    {
        Penjualan::where('tipe', 'online')->where('status', 'Pending')->whereNotNull('expired_at')->where('expired_at', '<=', now())
            ->orderBy('id')->chunkById(100, function ($orders) {
                foreach ($orders as $order) $order->cancelAndRestock();
            });
        $this->info('Pemeriksaan pesanan kedaluwarsa selesai.');
        return self::SUCCESS;
    }
}
