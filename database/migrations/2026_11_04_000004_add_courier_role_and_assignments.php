<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A string column keeps roles extensible (admin, kasir, pelanggan, kurir).
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('pelanggan')->change();
        });

        Schema::table('penjualans', function (Blueprint $table) {
            $table->foreignId('kurir_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->timestamp('kurir_assigned_at')->nullable()->after('kurir_id');
            $table->timestamp('dikirim_pada')->nullable()->after('kurir_assigned_at');
            $table->timestamp('diterima_pada')->nullable()->after('dikirim_pada');
        });
    }

    public function down(): void
    {
        if (DB::table('users')->where('role', 'kurir')->exists()) {
            throw new RuntimeException('Hapus atau ubah semua akun kurir sebelum rollback migrasi ini.');
        }

        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropForeign(['kurir_id']);
            $table->dropColumn(['kurir_id', 'kurir_assigned_at', 'dikirim_pada', 'diterima_pada']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'kasir', 'pelanggan'])->default('pelanggan')->change();
        });
    }
};
