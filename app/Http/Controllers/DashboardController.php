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
            $totalTrips = Trip::count();
            $totalBookings = Booking::count();
            $completedBookings = Booking::where('status', 'selesai')->count();
            $totalRevenue = Booking::where('status', 'selesai')->sum('total_harga');
            $recentBookings = Booking::with(['user', 'trip'])->latest()->limit(5)->get();
            $trips = Trip::where('status', 'aktif')->get();

            return view('admin.dashboard', compact(
                'totalTrips', 'totalBookings', 'completedBookings',
                'totalRevenue', 'recentBookings', 'trips'
            ));
        }

        $trips = Trip::where('status', 'aktif')->get();
        $myBookings = Booking::where('user_id', Auth::id())
            ->with('trip')->latest()->limit(3)->get();

        return view('customer.dashboard', compact('trips', 'myBookings'));
    }

    /**
     * Halaman detail trip
     */
    public function detail()
    {
        $trips = Trip::where('status', 'aktif')->orderBy('created_at', 'desc')->get();
        return view('detail', compact('trips'));
    }

    /**
     * Halaman transaksi (form pemesanan)
     */
    public function transaksi()
    {
        $trips = Trip::where('status', 'aktif')->get();

        $paymentInfo     = session('transaksi_info.payment_info',     'DP 50% dari total harga<br>Sisa pembayaran dapat diinformasikan via WhatsApp<br>Admin akan menghubungi Anda setelah booking dikonfirmasi');
        $termsConditions = session('transaksi_info.terms_conditions', 'Booking dianggap sah setelah upload bukti transfer<br>Pembatalan H-7: refund 50%<br>Pembatalan H-3: refund 25%<br>Pembatalan H-1: tidak ada refund');
        $quotaInfo       = session('transaksi_info.quota_info',       'Setiap paket trip maksimal 8 orang per tanggal keberangkatan<br>Silakan pilih tanggal yang masih tersedia kuotanya');
        $whatsappNumber  = session('transaksi_info.whatsapp_number',  '6282340188130');
        $contactText     = session('transaksi_info.contact_text',     'Butuh bantuan? Hubungi Admin');

        // Data bank (baru)
        $bankName   = session('transaksi_info.bank_name',   'BRI');
        $bankNumber = session('transaksi_info.bank_number', '1234567890');
        $bankOwner  = session('transaksi_info.bank_owner',  'a.n. Admin NajaTrip');

        // Gabungkan menjadi satu string untuk ditampilkan
        $bankInfo = $bankName . '<br>' . $bankNumber . '<br>' . $bankOwner;

        return view('transaksi', compact(
            'trips', 'paymentInfo', 'termsConditions', 'quotaInfo',
            'whatsappNumber', 'contactText', 'bankInfo', 'bankName', 'bankNumber', 'bankOwner'
        ));
    }

    /**
     * Update informasi transaksi (khusus admin)
     */
    public function updateInfoTransaksi(\Illuminate\Http\Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses!'], 403);
        }

        $request->validate([
            'payment_info'     => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'quota_info'       => 'nullable|string',
            'whatsapp_number'  => 'nullable|string',
            'contact_text'     => 'nullable|string',
            'bank_name'        => 'nullable|string',
            'bank_number'      => 'nullable|string',
            'bank_owner'       => 'nullable|string',
        ]);

        $info = [
            'payment_info'     => $request->payment_info,
            'terms_conditions' => $request->terms_conditions,
            'quota_info'       => $request->quota_info,
            'whatsapp_number'  => $request->whatsapp_number,
            'contact_text'     => $request->contact_text,
            'bank_name'        => $request->bank_name,
            'bank_number'      => $request->bank_number,
            'bank_owner'       => $request->bank_owner,
        ];

        session(['transaksi_info' => $info]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $info,
                'message' => 'Informasi berhasil diupdate!'
            ]);
        }

        return redirect()->route('transaksi')->with('success', 'Informasi transaksi berhasil diupdate!');
    }

    /**
     * Halaman edit informasi transaksi (admin)
     */
    public function editInfoTransaksi()
    {
        if (!Auth::user()->isAdmin()) abort(403);

        $info = [
            'payment_info'     => session('transaksi_info.payment_info',     'DP 50% dari total harga<br>Sisa pembayaran dapat diinformasikan via WhatsApp<br>Admin akan menghubungi Anda setelah booking dikonfirmasi'),
            'terms_conditions' => session('transaksi_info.terms_conditions', 'Booking dianggap sah setelah upload bukti transfer<br>Pembatalan H-7: refund 50%<br>Pembatalan H-3: refund 25%<br>Pembatalan H-1: tidak ada refund'),
            'quota_info'       => session('transaksi_info.quota_info',       'Setiap paket trip maksimal 8 orang per tanggal keberangkatan'),
            'whatsapp_number'  => session('transaksi_info.whatsapp_number',  '6282340188130'),
            'contact_text'     => session('transaksi_info.contact_text',     'Butuh bantuan? Hubungi Admin'),
            'bank_name'        => session('transaksi_info.bank_name',        'BRI'),
            'bank_number'      => session('transaksi_info.bank_number',      '1234567890'),
            'bank_owner'       => session('transaksi_info.bank_owner',       'a.n. Admin NajaTrip'),
        ];

        return view('admin.edit-info-transaksi', compact('info'));
    }

    /**
     * Halaman riwayat pemesanan
     */
    public function riwayat()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with('trip')->latest()->get();

        $totalBookings    = $bookings->count();
        $pendingCount     = $bookings->where('status', 'pending')->count();
        $dikonfirmasiCount = $bookings->where('status', 'dikonfirmasi')->count();
        $selesaiCount     = $bookings->where('status', 'selesai')->count();
        $totalSpent       = $bookings->where('status', 'selesai')->sum('total_harga');

        return view('riwayat', compact(
            'bookings', 'totalBookings', 'pendingCount',
            'dikonfirmasiCount', 'selesaiCount', 'totalSpent'
        ));
    }
}
