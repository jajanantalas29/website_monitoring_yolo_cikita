<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kartu extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi
    protected $fillable = [
        'pelanggan_id',
        'uid_kartu',
    ];

    // Relasi: 1 Kartu ini milik 1 Pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }
}