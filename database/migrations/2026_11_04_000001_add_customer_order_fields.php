<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telepon', 30)->nullable()->after('email');
            $table->text('alamat')->nullable()->after('telepon');
        });
        Schema::table('penjualans', function (Blueprint $table) {
            $table->string('nama_penerima')->nullable()->after('alamat_pengiriman');
            $table->string('telepon_penerima', 30)->nullable()->after('nama_penerima');
            $table->bigInteger('ongkir')->default(0)->after('ppn');
            $table->timestamp('expired_at')->nullable()->index()->after('tanggal');
            $table->string('nomor_resi')->nullable()->after('expired_at');
            $table->string('status', 30)->default('Pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn(['nama_penerima', 'telepon_penerima', 'ongkir', 'expired_at', 'nomor_resi']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['telepon', 'alamat']);
        });
    }
};
