<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'alamat',
        'telepon',
        'email',
    ];

    public function pasoks()
    {
        return $this->hasMany(Pasok::class, 'distributor_id');
    }
}
