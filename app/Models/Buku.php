<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'bukus';

    protected $fillable = [
        'kode',
        'isbn',
        'judul',
        'kategori',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'harga_pokok',
        'harga_jual',
        'diskon',
        'stok',
        'lokasi_rak',
        'cover_url',
        'status', // 'publikasi' | 'draf'
        'sinopsis',
    ];

    protected $casts = [
        'harga_pokok' => 'integer',
        'harga_jual' => 'integer',
        'diskon' => 'integer',
        'stok' => 'integer',
        'tahun_terbit' => 'integer',
    ];

    public function detailPenjualans()
    {
        return $this->hasMany(DetailPenjualan::class, 'buku_id');
    }

    public function pasoks()
    {
        return $this->hasMany(Pasok::class, 'buku_id');
    }

    public function getHargaNetAttribute()
    {
        if ($this->diskon > 0) {
            return (int) round($this->harga_jual * (100 - min(100, max(0, (int) $this->diskon))) / 100);
        }
        return $this->harga_jual;
    }
}
