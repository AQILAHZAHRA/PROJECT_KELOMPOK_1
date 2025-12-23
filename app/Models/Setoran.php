<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setoran extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',      // foreign key ke tabel users
        'jumlah',       // jumlah setoran
        'total_berat',  // total berat sampah
        'items',        // detail items sampah (JSON format)
        'keterangan'    // opsional
    ];

    // Relasi: Setoran milik satu User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
