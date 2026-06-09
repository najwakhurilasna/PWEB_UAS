<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 'trip_id', 'nama_lengkap', 'nomor_telepon',
        'tanggal_berangkat', 'jumlah_peserta', 'total_harga',
        'ktp_path', 'bukti_path', 'status', 'catatan'
    ];

    protected $casts = [
        'tanggal_berangkat' => 'date',
        'total_harga' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Hitung total peserta untuk trip tertentu di tanggal tertentu
     * (Untuk cek kuota max 8 orang per trip)
     */
    public static function getTotalPesertaByDate($tripId, $tanggal)
    {
        return self::where('trip_id', $tripId)
            ->where('tanggal_berangkat', $tanggal)
            ->whereNotIn('status', ['batal'])
            ->sum('jumlah_peserta');
    }

    /**
     * Cek kuota per trip (max 8 orang) - LANGSUNG CEK
     */
    public static function isQuotaAvailable($tripId, $tanggal, $jumlahPeserta)
    {
        $total = self::getTotalPesertaByDate($tripId, $tanggal);
        $totalBaru = $total + $jumlahPeserta;

        // Jika total melebihi 8, TIDAK BOLEH
        if ($totalBaru > 8) {
            return false;
        }

        return true;
    }

    /**
     * Hitung jumlah trip BERBEDA yang sudah dibooking di tanggal tertentu
     * (Untuk cek max 2 trip berbeda per tanggal)
     */
    public static function getDistinctTripCountByDate($tanggal)
    {
        return self::where('tanggal_berangkat', $tanggal)
            ->whereNotIn('status', ['batal'])
            ->distinct('trip_id')
            ->count('trip_id');
    }

    /**
     * Cek apakah masih bisa menambah TRIP BERBEDA di tanggal tertentu (max 2 trip berbeda)
     */
    public static function canAddNewTrip($tanggal, $currentTripId)
    {
        // Hitung jumlah trip berbeda di tanggal tersebut
        $distinctTrips = self::where('tanggal_berangkat', $tanggal)
            ->whereNotIn('status', ['batal'])
            ->distinct('trip_id')
            ->pluck('trip_id')
            ->toArray();

        $jumlahTripBerbeda = count($distinctTrips);

        // Cek apakah trip yang akan dipesan sudah ada di tanggal tersebut
        $isExistingTrip = in_array($currentTripId, $distinctTrips);

        // Jika trip sudah ada (trip yang sama) → BOLEH (tambah peserta)
        if ($isExistingTrip) {
            return true;
        }

        // Jika trip berbeda dan jumlah trip berbeda sudah mencapai 2 → TIDAK BOLEH
        if ($jumlahTripBerbeda >= 2) {
            return false;
        }

        // Jika trip berbeda dan jumlah trip berbeda kurang dari 2 → BOLEH
        return true;
    }
}
