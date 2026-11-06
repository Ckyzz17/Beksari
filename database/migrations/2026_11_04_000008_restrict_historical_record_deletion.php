<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });

        Schema::table('pasoks', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->dropForeign(['buku_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->restrictOnDelete();
            $table->foreign('buku_id')->references('id')->on('bukus')->restrictOnDelete();
        });

        Schema::table('detail_penjualans', function (Blueprint $table) {
            $table->dropForeign(['penjualan_id']);
            $table->dropForeign(['buku_id']);
            $table->foreign('penjualan_id')->references('id')->on('penjualans')->restrictOnDelete();
            $table->foreign('buku_id')->references('id')->on('bukus')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('pasoks', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->dropForeign(['buku_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->cascadeOnDelete();
            $table->foreign('buku_id')->references('id')->on('bukus')->cascadeOnDelete();
        });

        Schema::table('detail_penjualans', function (Blueprint $table) {
            $table->dropForeign(['penjualan_id']);
            $table->dropForeign(['buku_id']);
            $table->foreign('penjualan_id')->references('id')->on('penjualans')->cascadeOnDelete();
            $table->foreign('buku_id')->references('id')->on('bukus')->cascadeOnDelete();
        });
    }
};
