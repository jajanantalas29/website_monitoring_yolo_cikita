<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    // Tambahkan 'embedding' ke dalam fillable
    protected $fillable = [
        'nama_lengkap',
        'nomor_telepon',
        'foto_lurus',
        'foto_kiri',
        'foto_kanan',
        'foto_mulut',
        'embedding',
        'status_ruangan',
    ];

    // Trik agar Laravel otomatis mengkonversi array 512 angka ke JSON saat disimpan, 
    // dan mengembalikannya jadi array saat dipanggil.
    protected $casts = [
        'embedding' => 'array',
    ];

    // Relasi: 1 Pelanggan memiliki 1 Kartu (Sudah kamu buat sebelumnya)
    public function kartu()
    {
        return $this->hasOne(Kartu::class);
    }
}