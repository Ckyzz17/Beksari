<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasok extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_id',
        'buku_id',
        'jumlah',
        'tanggal',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'tanggal' => 'date',
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_id');
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id');
    }
}
