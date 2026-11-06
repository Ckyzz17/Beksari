<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukus', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('isbn', 30)->unique();
            $table->string('judul', 255);
            $table->string('kategori', 100)->index();
            $table->string('penulis', 150);
            $table->string('penerbit', 150)->nullable();
            $table->year('tahun_terbit')->default(2024);
            $table->bigInteger('harga_pokok');
            $table->bigInteger('harga_jual');
            $table->integer('diskon')->default(0);
            $table->integer('stok')->default(0);
            $table->string('lokasi_rak', 50)->nullable();
            $table->text('cover_url')->nullable();
            $table->enum('status', ['publikasi', 'draf'])->default('publikasi');
            $table->text('sinopsis')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukus');
    }
};
