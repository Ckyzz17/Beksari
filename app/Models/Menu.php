<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    // protected $fillable = [
    //     'kategori_id',
    //     'nama',
    //     'harga',
    //     'deskripsi',
    //     'tersedia',
    // ];

    protected $casts = [
        'harga'    => 'decimal:2',
        'tersedia' => 'boolean',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}
