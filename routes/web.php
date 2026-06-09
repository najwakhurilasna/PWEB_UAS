<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\WeatherController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==================== HALAMAN DEPAN ====================
Route::get('/', function () {
    return redirect('/dashboard');
});

// ==================== ROUTE CUSTOMER (WAJIB LOGIN) ====================
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/detail', [DashboardController::class, 'detail'])->name('detail');
    Route::get('/transaksi', [DashboardController::class, 'transaksi'])->name('transaksi');
    Route::post('/transaksi', [BookingController::class, 'store'])->name('transaksi.store');
    Route::get('/riwayat', [DashboardController::class, 'riwayat'])->name('riwayat');
    Route::post('/booking/{id}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
});

// ==================== ROUTE ADMIN (LOGIN + ROLE ADMIN) ====================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // MANAJEMEN TRIP (CRUD)
    Route::get('/trip/create', [TripController::class, 'create'])->name('trip.create');
    Route::post('/trip', [TripController::class, 'store'])->name('trip.store');
    Route::get('/trip/{id}/edit', [TripController::class, 'edit'])->name('trip.edit');
    Route::put('/trip/{id}', [TripController::class, 'update'])->name('trip.update');
    Route::delete('/trip/{id}', [TripController::class, 'destroy'])->name('trip.destroy');

    // MANAJEMEN BOOKING / PESANAN
    Route::get('/daftar', [BookingController::class, 'adminDaftar'])->name('daftar');
    Route::put('/booking/{id}/status', [BookingController::class, 'updateStatus'])->name('booking.status');

    // EDIT INFORMASI TRANSAKSI
    Route::get('/edit-info-transaksi', [DashboardController::class, 'editInfoTransaksi'])->name('edit-info-transaksi');
    Route::post('/update-info-transaksi', [DashboardController::class, 'updateInfoTransaksi'])->name('update-info-transaksi');
});

// ==================== API ROUTES ====================
Route::get('/api/weather', [WeatherController::class, 'getWeather']);
Route::get('/api/check-quota', [BookingController::class, 'checkQuota']);

// ==================== LUPA PASSWORD (SUPER MUDAH, TANPA CONTROLLER) ====================
Route::get('/lupa-password', function () {
    return view('auth.lupa-password');
})->name('lupa.password');

Route::post('/lupa-password', function (Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'password' => 'required|min:4|confirmed'
    ]);

    $user = App\Models\User::where('email', $request->email)->first();
    $user->password = Illuminate\Support\Facades\Hash::make($request->password);
    $user->save();

    return redirect()->route('login')->with('success', 'Password berhasil diubah! Silakan login dengan password baru.');
})->name('lupa.password.post');

// ==================== AUTH ROUTES ====================
require __DIR__.'/auth.php';
