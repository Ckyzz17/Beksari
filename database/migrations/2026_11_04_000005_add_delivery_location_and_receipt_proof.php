<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('alamat_pengiriman');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('bukti_penerimaan_path')->nullable()->after('payment_proof_path');
            $table->timestamp('bukti_penerimaan_uploaded_at')->nullable()->after('bukti_penerimaan_path');
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn([
                'latitude',
                'longitude',
                'bukti_penerimaan_path',
                'bukti_penerimaan_uploaded_at',
            ]);
        });
    }
};
