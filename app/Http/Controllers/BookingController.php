<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Store a new booking
     */
    public function store(Request $request)
    {
        $request->validate([
            'trip_id'          => 'required|exists:trips,id',
            'nama_lengkap'     => 'required|string|max:255',
            'nomor_telepon'    => 'required|string|max:20',
            'tanggal_berangkat'=> 'required|date|after:today',
            'jumlah_peserta'   => 'required|integer|min:1|max:8',
            'ktp'              => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'bukti'            => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'catatan'          => 'nullable|string',
        ]);

        $trip    = Trip::findOrFail($request->trip_id);
        $tanggal = $request->tanggal_berangkat;

        // CEK KUOTA 1: MAX 8 ORANG PER TRIP
        if (!Booking::isQuotaAvailable($trip->id, $tanggal, $request->jumlah_peserta)) {
            $totalTerisi = Booking::getTotalPesertaByDate($trip->id, $tanggal);
            return redirect()->back()
                ->with('error', "Maaf, kuota trip ini sudah penuh! (Sudah {$totalTerisi}/8 orang, Anda ingin tambah {$request->jumlah_peserta} orang)")
                ->withInput();
        }

        // CEK KUOTA 2: MAX 2 TRIP BERBEDA PER TANGGAL
        if (!Booking::canAddNewTrip($tanggal, $trip->id)) {
            $distinctTrips = Booking::getDistinctTripCountByDate($tanggal);
            return redirect()->back()
                ->with('error', "Maaf, tanggal ini sudah memiliki {$distinctTrips} trip berbeda. Maksimal 2 trip berbeda per hari!")
                ->withInput();
        }

        $ktpPath   = $request->file('ktp')->store('bookings/ktp', 'public');
        $buktiPath = $request->file('bukti')->store('bookings/bukti', 'public');

        Booking::create([
            'user_id'          => Auth::id(),
            'trip_id'          => $request->trip_id,
            'nama_lengkap'     => $request->nama_lengkap,
            'nomor_telepon'    => $request->nomor_telepon,
            'tanggal_berangkat'=> $tanggal,
            'jumlah_peserta'   => $request->jumlah_peserta,
            'total_harga'      => $trip->harga * $request->jumlah_peserta,
            'ktp_path'         => $ktpPath,
            'bukti_path'       => $buktiPath,
            'status'           => 'pending',
            'catatan'          => $request->catatan,
        ]);

        return redirect()->route('riwayat')->with('success', 'Booking berhasil! Admin akan menghubungi Anda.');
    }

    /**
     * Customer cancel booking (only if status is pending)
     */
    public function cancel($id)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($id);

        if ($booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak dapat dibatalkan karena sudah ' . $booking->status,
            ], 400);
        }

        $booking->status = 'batal';
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dibatalkan',
        ]);
    }

    /**
     * API to check quota (AJAX) - REALTIME
     */
    public function checkQuota(Request $request)
    {
        $tripId  = $request->trip_id;
        $tanggal = $request->tanggal;

        $totalPeserta = Booking::getTotalPesertaByDate($tripId, $tanggal);
        $sisaKuota    = max(0, 8 - $totalPeserta);
        $isQuotaFull  = ($sisaKuota <= 0);

        $distinctTrips = Booking::where('tanggal_berangkat', $tanggal)
            ->whereNotIn('status', ['batal'])
            ->distinct('trip_id')
            ->pluck('trip_id')
            ->toArray();

        $jumlahTripBerbeda   = count($distinctTrips);
        $isTripAlreadyBooked = in_array($tripId, $distinctTrips);

        $canBookNewTrip = $isTripAlreadyBooked ? true : ($jumlahTripBerbeda < 2);

        return response()->json([
            'sisa_kuota'           => $sisaKuota,
            'total_terpakai'       => $totalPeserta,
            'max_kuota'            => 8,
            'is_quota_full'        => $isQuotaFull,
            'jumlah_trip_berbeda'  => $jumlahTripBerbeda,
            'max_trip_berbeda'     => 2,
            'is_trip_already_booked' => $isTripAlreadyBooked,
            'can_book_new_trip'    => $canBookNewTrip,
            'list_trip_ids'        => $distinctTrips,
        ]);
    }

    /**
     * Admin: View all bookings
     */
    public function adminDaftar()
    {
        $bookings = Booking::with(['user', 'trip'])->latest()->get();
        $today    = Carbon::today();

        // Tambahkan info allowed status per booking
        $bookings->each(function ($booking) use ($today) {
            $booking->allowed_statuses = $this->getAllowedStatuses($booking, $today);
        });

        return view('admin.daftar', compact('bookings'));
    }

    /**
     * Admin: Update booking status — dengan validasi logika
     */
    public function updateStatus(Request $request, $id)
    {
        $booking   = Booking::findOrFail($id);
        $newStatus = $request->status;
        $today     = Carbon::today();
        $allowed   = $this->getAllowedStatuses($booking, $today);

        // Cek apakah status yang dipilih diizinkan
        if (!in_array($newStatus, $allowed)) {
            $reason = $this->getBlockReason($booking, $newStatus, $today);
            return redirect()->back()->with('error', $reason);
        }

        $booking->status = $newStatus;
        $booking->save();

        return redirect()->back()->with('success', 'Status booking berhasil diupdate!');
    }

    /**
     * Tentukan daftar status yang boleh dipilih berdasarkan kondisi booking
     *
     * Aturan:
     * - 'selesai' hanya bisa dipilih jika tanggal berangkat SUDAH LEWAT (hari ini atau sebelumnya)
     * - 'batal' tidak bisa dipilih jika status saat ini sudah 'selesai'
     * - Status saat ini selalu ada dalam list (agar select tidak error)
     */
    private function getAllowedStatuses(Booking $booking, Carbon $today): array
    {
        $tanggal         = Carbon::parse($booking->tanggal_berangkat);
        $sudahLewat      = $tanggal->lte($today); // tanggal berangkat <= hari ini
        $currentStatus   = $booking->status;

        $statuses = ['pending', 'dikonfirmasi'];

        // 'selesai' hanya bisa jika tanggal sudah lewat
        if ($sudahLewat) {
            $statuses[] = 'selesai';
        }

        // 'batal' tidak bisa jika sudah selesai
        if ($currentStatus !== 'selesai') {
            $statuses[] = 'batal';
        }

        // Pastikan status saat ini selalu ada (untuk konsistensi tampilan)
        if (!in_array($currentStatus, $statuses)) {
            $statuses[] = $currentStatus;
        }

        return $statuses;
    }

    /**
     * Pesan error ketika status tidak boleh diubah
     */
    private function getBlockReason(Booking $booking, string $newStatus, Carbon $today): string
    {
        $tanggal    = Carbon::parse($booking->tanggal_berangkat);
        $tanggalStr = $tanggal->translatedFormat('d F Y');

        if ($newStatus === 'selesai' && $tanggal->gt($today)) {
            return "Status tidak bisa diubah ke 'Selesai' karena trip belum dilaksanakan. Tanggal berangkat: {$tanggalStr}.";
        }

        if ($newStatus === 'batal' && $booking->status === 'selesai') {
            return "Booking yang sudah 'Selesai' tidak bisa dibatalkan.";
        }

        return "Perubahan status tidak diizinkan.";
    }
}
