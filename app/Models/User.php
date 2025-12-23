<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Attributes yang bisa diisi massal
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'no_hp',
        'alamat',
        'saldo', // kolom saldo
    ];

    // Attributes yang disembunyikan saat serialization
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Casting atribut otomatis
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relasi: User memiliki banyak Setoran
    public function setoran()
    {
        return $this->hasMany(Setoran::class);
    }

    // Contoh method untuk menghitung total saldo dari setoran
    public function totalSaldo()
    {
        return $this->setoran()->sum('jumlah'); // pastikan kolom 'jumlah' ada di tabel setoran
    }
}
