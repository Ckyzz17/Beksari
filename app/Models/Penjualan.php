<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Penjualan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_transaksi',
        'checkout_token',
        'user_id',
        'kurir_id',
        'kurir_assigned_at',
        'dikirim_pada',
        'diterima_pada',
        'subtotal',
        'ppn',
        'diskon',
        'total_bayar',
        'metode_pembayaran', // 'Tunai' | 'Kartu' | 'QRIS'
        'bayar',
        'kembali',
        'status', // 'Completed' | 'Pending' | 'Cancelled'
        'tipe', // 'pos' | 'online'
        'alamat_pengiriman',
        'nama_penerima',
        'telepon_penerima',
        'ongkir',
        'expired_at',
        'nomor_resi',
        'catatan',
        'payment_proof_path',
        'latitude',
        'longitude',
        'bukti_penerimaan_path',
        'bukti_penerimaan_uploaded_at',
        'tanggal',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'ppn' => 'integer',
        'ongkir' => 'integer',
        'expired_at' => 'datetime',
        'kurir_assigned_at' => 'datetime',
        'dikirim_pada' => 'datetime',
        'diterima_pada' => 'datetime',
        'bukti_penerimaan_uploaded_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'diskon' => 'integer',
        'total_bayar' => 'integer',
        'bayar' => 'integer',
        'kembali' => 'integer',
        'tanggal' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details()
    {
        return $this->hasMany(DetailPenjualan::class, 'penjualan_id');
    }

    public function kurir()
    {
        return $this->belongsTo(User::class, 'kurir_id');
    }

    public function isOnline(): bool
    {
        return $this->tipe === 'online';
    }

    /**
     * Batalkan pesanan Pending dan kembalikan stok. Return false jika status sudah bukan Pending.
     */
    public function cancelAndRestock(): bool
    {
        return DB::transaction(function () {
            $fresh = static::lockForUpdate()->find($this->id);
            if (! $fresh || $fresh->status !== 'Pending') {
                return false;
            }

            foreach ($fresh->details as $d) {
                Buku::where('id', $d->buku_id)->increment('stok', $d->jumlah);
            }
            $fresh->update(['status' => 'Cancelled']);
            $this->status = 'Cancelled';

            return true;
        });
    }
}
