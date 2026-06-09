<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $fillable = [
        'nama', 'lokasi', 'deskripsi', 'harga', 'durasi', 'fasilitas', 'status'
    ];

    protected $casts = [
        'fasilitas' => 'array',
        'harga' => 'integer',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
