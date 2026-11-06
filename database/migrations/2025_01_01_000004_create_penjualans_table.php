<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->bigInteger('subtotal');
            $table->bigInteger('ppn')->default(0);
            $table->bigInteger('diskon')->default(0);
            $table->bigInteger('total_bayar');
            $table->string('metode_pembayaran', 20)->default('Tunai'); // Tunai, Kartu, QRIS
            $table->bigInteger('bayar')->default(0);
            $table->bigInteger('kembali')->default(0);
            $table->enum('status', ['Completed', 'Pending', 'Cancelled'])->default('Completed');
            $table->string('tipe', 10)->default('pos'); // 'pos' = kasir, 'online' = pesanan pelanggan
            $table->text('alamat_pengiriman')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
