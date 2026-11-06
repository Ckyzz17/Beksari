<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_penjualans', function (Blueprint $table) {
            $table->unsignedBigInteger('harga_pokok_satuan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('detail_penjualans', function (Blueprint $table) {
            $table->dropColumn('harga_pokok_satuan');
        });
    }
};
