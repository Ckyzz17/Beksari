<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'telepon',
        'alamat',
        'password',
        'role', // 'admin' | 'kasir' | 'pelanggan' | 'kurir'
        'avatar_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function penjualans()
    {
        return $this->hasMany(Penjualan::class, 'user_id');
    }

    public function assignedDeliveries()
    {
        return $this->hasMany(Penjualan::class, 'kurir_id');
    }
}
