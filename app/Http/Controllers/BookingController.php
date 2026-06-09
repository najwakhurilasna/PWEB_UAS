<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    /**
     * Store a new booking
     */
    public function store(Request $request)
    {
        $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'nama_lengkap' => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:20',
            'tanggal_berangkat' => 'required|date|after:today',
            'jumlah_peserta' => 'required|integer|min:1|max:8',
            'ktp' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'bukti' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'catatan' => 'nullable|string'
        ]);

        $trip = Trip::findOrFail($request->trip_id);
        $tanggal = $request->tanggal_berangkat;

        // ========== CEK KUOTA 1: MAX 8 ORANG PER TRIP ==========
        if (!Booking::isQuotaAvailable($trip->id, $tanggal, $request->jumlah_peserta)) {
            $totalTerisi = Booking::getTotalPesertaByDate($trip->id, $tanggal);
            return redirect()->back()
                ->with('error', "Maaf, kuota trip ini sudah penuh! (Sudah {$totalTerisi}/8 orang, Anda ingin tambah {$request->jumlah_peserta} orang)")
                ->withInput();
        }

        // ========== CEK KUOTA 2: MAX 2 TRIP BERBEDA PER TANGGAL ==========
        if (!Booking::canAddNewTrip($tanggal, $trip->id)) {
            $distinctTrips = Booking::getDistinctTripCountByDate($tanggal);
            return redirect()->back()
                ->with('error', "Maaf, tanggal ini sudah memiliki {$distinctTrips} trip berbeda. Maksimal 2 trip berbeda per hari!")
                ->withInput();
        }

        // Upload files
        $ktpPath = $request->file('ktp')->store('bookings/ktp', 'public');
        $buktiPath = $request->file('bukti')->store('bookings/bukti', 'public');

        // Save booking
        Booking::create([
            'user_id' => Auth::id(),
            'trip_id' => $request->trip_id,
            'nama_lengkap' => $request->nama_lengkap,
            'nomor_telepon' => $request->nomor_telepon,
            'tanggal_berangkat' => $tanggal,
            'jumlah_peserta' => $request->jumlah_peserta,
            'total_harga' => $trip->harga * $request->jumlah_peserta,
            'ktp_path' => $ktpPath,
            'bukti_path' => $buktiPath,
            'status' => 'pending',
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('riwayat')->with('success', 'Booking berhasil! Admin akan menghubungi Anda.');
    }

    /**
     * Customer cancel booking (only if status is pending)
     */
    public function cancel($id)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($id);

        if ($booking->status != 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak dapat dibatalkan karena sudah ' . $booking->status
            ], 400);
        }

        $booking->status = 'batal';
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dibatalkan'
        ]);
    }

    /**
     * API to check quota (AJAX) - REALTIME
     */
    public function checkQuota(Request $request)
    {
        $tripId = $request->trip_id;
        $tanggal = $request->tanggal;

        // Data 1: Quota per trip (max 8 people)
        $totalPeserta = Booking::getTotalPesertaByDate($tripId, $tanggal);
        $sisaKuota = max(0, 8 - $totalPeserta);
        $isQuotaFull = ($sisaKuota <= 0);

        // Data 2: Different trips per date (max 2 trips)
        $distinctTrips = Booking::where('tanggal_berangkat', $tanggal)
            ->whereNotIn('status', ['batal'])
            ->distinct('trip_id')
            ->pluck('trip_id')
            ->toArray();

        $jumlahTripBerbeda = count($distinctTrips);
        $isTripAlreadyBooked = in_array($tripId, $distinctTrips);

        // Can book a new trip?
        $canBookNewTrip = false;
        if ($isTripAlreadyBooked) {
            // Same trip → ALLOWED (as long as quota is available)
            $canBookNewTrip = true;
        } else {
            // Different trip → ALLOWED if less than 2 different trips
            $canBookNewTrip = ($jumlahTripBerbeda < 2);
        }

        return response()->json([
            // Per trip quota data
            'sisa_kuota' => $sisaKuota,
            'total_terpakai' => $totalPeserta,
            'max_kuota' => 8,
            'is_quota_full' => $isQuotaFull,

            // Per date different trips data
            'jumlah_trip_berbeda' => $jumlahTripBerbeda,
            'max_trip_berbeda' => 2,
            'is_trip_already_booked' => $isTripAlreadyBooked,
            'can_book_new_trip' => $canBookNewTrip,
            'list_trip_ids' => $distinctTrips
        ]);
    }

    /**
     * Admin: View all bookings
     */
    public function adminDaftar()
    {
        $bookings = Booking::with(['user', 'trip'])->latest()->get();
        return view('admin.daftar', compact('bookings'));
    }

    /**
     * Admin: Update booking status
     */
    public function updateStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = $request->status;
        $booking->save();

        return redirect()->back()->with('success', 'Status booking berhasil diupdate!');
    }
}
