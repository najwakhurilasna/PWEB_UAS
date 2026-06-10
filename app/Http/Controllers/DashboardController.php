<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard utama (berbeda untuk admin dan customer)
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $totalTrips      = Trip::count();
            $totalBookings   = Booking::count();
            $completedBookings = Booking::where('status', 'selesai')->count();
            $totalRevenue    = Booking::where('status', 'selesai')->sum('total_harga');
            $recentBookings  = Booking::with(['user', 'trip'])->latest()->limit(5)->get();
            $trips           = Trip::where('status', 'aktif')->get();

            return view('admin.dashboard', compact(
                'totalTrips', 'totalBookings', 'completedBookings',
                'totalRevenue', 'recentBookings', 'trips'
            ));
        }

        $trips     = Trip::where('status', 'aktif')->get();
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
     * Halaman transaksi (form pemesanan) — baca dari database
     */
    public function transaksi()
    {
        $trips = Trip::where('status', 'aktif')->get();

        $paymentInfo     = Setting::get('payment_info',     'DP 50% dari total harga<br>Sisa pembayaran dapat diinformasikan via WhatsApp<br>Admin akan menghubungi Anda setelah booking dikonfirmasi');
        $termsConditions = Setting::get('terms_conditions', 'Booking dianggap sah setelah upload bukti transfer<br>Pembatalan H-7: refund 50%<br>Pembatalan H-3: refund 25%<br>Pembatalan H-1: tidak ada refund');
        $quotaInfo       = Setting::get('quota_info',       'Setiap paket trip maksimal 8 orang per tanggal keberangkatan<br>Silakan pilih tanggal yang masih tersedia kuotanya');
        $whatsappNumber  = Setting::get('whatsapp_number',  '6282340188130');
        $contactText     = Setting::get('contact_text',     'Butuh bantuan? Hubungi Admin');
        $bankName        = Setting::get('bank_name',        'BRI');
        $bankNumber      = Setting::get('bank_number',      '1234567890');
        $bankOwner       = Setting::get('bank_owner',       'a.n. Admin NajaTrip');

        $bankInfo = $bankName . '<br>' . $bankNumber . '<br>' . $bankOwner;

        return view('transaksi', compact(
            'trips', 'paymentInfo', 'termsConditions', 'quotaInfo',
            'whatsappNumber', 'contactText', 'bankInfo', 'bankName', 'bankNumber', 'bankOwner'
        ));
    }

    /**
     * Update informasi transaksi (khusus admin) — simpan ke database
     */
    public function updateInfoTransaksi(Request $request)
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

        // Simpan ke database (permanen)
        $fields = [
            'payment_info', 'terms_conditions', 'quota_info',
            'whatsapp_number', 'contact_text',
            'bank_name', 'bank_number', 'bank_owner',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                Setting::set($field, $request->input($field, ''));
            }
        }

        $info = [];
        foreach ($fields as $field) {
            $info[$field] = Setting::get($field);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $info,
                'message' => 'Informasi berhasil diupdate!',
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
            'payment_info'     => Setting::get('payment_info',     'DP 50% dari total harga<br>Sisa pembayaran dapat diinformasikan via WhatsApp'),
            'terms_conditions' => Setting::get('terms_conditions', 'Booking dianggap sah setelah upload bukti transfer'),
            'quota_info'       => Setting::get('quota_info',       'Setiap paket trip maksimal 8 orang per tanggal keberangkatan'),
            'whatsapp_number'  => Setting::get('whatsapp_number',  '6282340188130'),
            'contact_text'     => Setting::get('contact_text',     'Butuh bantuan? Hubungi Admin'),
            'bank_name'        => Setting::get('bank_name',        'BRI'),
            'bank_number'      => Setting::get('bank_number',      '1234567890'),
            'bank_owner'       => Setting::get('bank_owner',       'a.n. Admin NajaTrip'),
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

        $totalBookings     = $bookings->count();
        $pendingCount      = $bookings->where('status', 'pending')->count();
        $dikonfirmasiCount = $bookings->where('status', 'dikonfirmasi')->count();
        $selesaiCount      = $bookings->where('status', 'selesai')->count();
        $totalSpent        = $bookings->where('status', 'selesai')->sum('total_harga');

        return view('riwayat', compact(
            'bookings', 'totalBookings', 'pendingCount',
            'dikonfirmasiCount', 'selesaiCount', 'totalSpent'
        ));
    }
}
