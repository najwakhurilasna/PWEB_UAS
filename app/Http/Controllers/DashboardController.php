<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard utama (berbeda untuk admin dan customer)
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            // ========== DATA UNTUK ADMIN DASHBOARD ==========
            // Total Trip
            $totalTrips = Trip::count();

            // Total Booking (semua pemesanan)
            $totalBookings = Booking::count();

            // Trip Selesai (booking dengan status 'selesai')
            $completedBookings = Booking::where('status', 'selesai')->count();

            // Penghasilan (hanya dari booking yang statusnya 'selesai')
            $totalRevenue = Booking::where('status', 'selesai')->sum('total_harga');

            // Booking terbaru (5 terakhir) untuk ditampilkan di tabel
            $recentBookings = Booking::with(['user', 'trip'])
                ->latest()
                ->limit(5)
                ->get();

            // Semua trip aktif (untuk ditampilkan di dashboard)
            $trips = Trip::where('status', 'aktif')->get();

            return view('admin.dashboard', compact(
                'totalTrips',
                'totalBookings',
                'completedBookings',
                'totalRevenue',
                'recentBookings',
                'trips'
            ));
        }

        // ========== DATA UNTUK CUSTOMER DASHBOARD ==========
        // Trip aktif yang tersedia
        $trips = Trip::where('status', 'aktif')->get();

        // Booking milik customer yang login (3 terakhir)
        $myBookings = Booking::where('user_id', Auth::id())
            ->with('trip')
            ->latest()
            ->limit(3)
            ->get();

        return view('customer.dashboard', compact('trips', 'myBookings'));
    }

    /**
     * Halaman detail trip (daftar semua trip aktif)
     */
    public function detail()
    {
        $trips = Trip::where('status', 'aktif')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('detail', compact('trips'));
    }

    /**
     * Halaman transaksi (form pemesanan)
     * Informasi panel diambil dari session (bisa diedit admin)
     */
    public function transaksi()
    {
        $trips = Trip::where('status', 'aktif')->get();

        // Ambil data dari session (default values jika belum ada)
        $paymentInfo = session('transaksi_info.payment_info', 'DP 50% dari total harga<br>Sisa pembayaran dapat diinformasikan via WhatsApp<br>Admin akan menghubungi Anda setelah booking dikonfirmasi');
        $termsConditions = session('transaksi_info.terms_conditions', 'Booking dianggap sah setelah upload bukti transfer<br>Pembatalan H-7: refund 50%<br>Pembatalan H-3: refund 25%<br>Pembatalan H-1: tidak ada refund');
        $quotaInfo = session('transaksi_info.quota_info', 'Setiap paket trip maksimal 8 orang per tanggal keberangkatan<br>Silakan pilih tanggal yang masih tersedia kuotanya');
        $whatsappNumber = session('transaksi_info.whatsapp_number', '6282340188130');
        $contactText = session('transaksi_info.contact_text', 'Butuh bantuan? Hubungi Admin');

        return view('transaksi', compact(
            'trips',
            'paymentInfo',
            'termsConditions',
            'quotaInfo',
            'whatsappNumber',
            'contactText'
        ));
    }

    /**
     * Halaman edit informasi transaksi (khusus admin)
     */
    public function editInfoTransaksi()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $info = [
            'payment_info' => session('transaksi_info.payment_info', 'DP 50% dari total harga<br>Sisa pembayaran dapat diinformasikan via WhatsApp<br>Admin akan menghubungi Anda setelah booking dikonfirmasi'),
            'terms_conditions' => session('transaksi_info.terms_conditions', 'Booking dianggap sah setelah upload bukti transfer<br>Pembatalan H-7: refund 50%<br>Pembatalan H-3: refund 25%<br>Pembatalan H-1: tidak ada refund'),
            'quota_info' => session('transaksi_info.quota_info', 'Setiap paket trip maksimal 8 orang per tanggal keberangkatan<br>Silakan pilih tanggal yang masih tersedia kuotanya'),
            'whatsapp_number' => session('transaksi_info.whatsapp_number', '6282340188130'),
            'contact_text' => session('transaksi_info.contact_text', 'Butuh bantuan? Hubungi Admin')
        ];

        return view('admin.edit-info-transaksi', compact('info'));
    }

    /**
     * Update informasi transaksi (khusus admin)
     */
    public function updateInfoTransaksi(\Illuminate\Http\Request $request)
    {
        // Pastikan user adalah admin
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses!'
            ], 403);
        }

        // Validasi
        $request->validate([
            'payment_info' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'quota_info' => 'nullable|string',
            'whatsapp_number' => 'nullable|string',
            'contact_text' => 'nullable|string',
        ]);

        // Simpan ke session
        $info = [
            'payment_info' => $request->payment_info,
            'terms_conditions' => $request->terms_conditions,
            'quota_info' => $request->quota_info,
            'whatsapp_number' => $request->whatsapp_number,
            'contact_text' => $request->contact_text,
        ];

        session(['transaksi_info' => $info]);

        // Jika request dari AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $info,
                'message' => 'Informasi berhasil diupdate!'
            ]);
        }

        // Jika request dari form biasa
        return redirect()->route('transaksi')->with('success', 'Informasi transaksi berhasil diupdate!');
    }

    /**
     * Halaman riwayat pemesanan (untuk semua user yang login)
     */
    public function riwayat()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with('trip')
            ->latest()
            ->get();

        // Statistik untuk customer
        $totalBookings = $bookings->count();
        $pendingCount = $bookings->where('status', 'pending')->count();
        $dikonfirmasiCount = $bookings->where('status', 'dikonfirmasi')->count();
        $selesaiCount = $bookings->where('status', 'selesai')->count();
        $totalSpent = $bookings->where('status', 'selesai')->sum('total_harga');

        return view('riwayat', compact(
            'bookings',
            'totalBookings',
            'pendingCount',
            'dikonfirmasiCount',
            'selesaiCount',
            'totalSpent'
        ));
    }
}
