@extends('layouts.app')

@section('title', 'Dashboard - NajaTrip')

@section('content')
<div class="hero-gradient text-white p-5 rounded-4 mb-4 text-center">
    <h1 class="display-5 fw-bold">Selamat Datang di NajaTrip!</h1>
    <p class="lead">Jelajahi Pesona Banyuwangi & Bali bersama kami</p>
    <a href="{{ route('detail') }}" class="btn btn-light btn-lg mt-3">
        <i class="fas fa-search"></i> Lihat Semua Trip
    </a>
</div>

{{-- Weather Widget --}}
<div class="card shadow-sm mb-4" id="weatherWidget">
    <div class="card-body text-center py-5" id="weatherLoading">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-2 mb-0">Memuat data cuaca...</p>
    </div>
    <div id="weatherContent" class="card-body" style="display:none;"></div>
</div>

{{-- Info Booking --}}
<div class="alert alert-info info-panel mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div>
            <h5 class="mb-1"><i class="fas fa-info-circle"></i> Informasi Pemesanan</h5>
            <p class="mb-0 small">✅ DP 50% dari total harga<br>✅ Sisa pembayaran dapat diinformasikan via WhatsApp<br>✅ Admin akan menghubungi Anda setelah booking dikonfirmasi</p>
        </div>
        <a href="https://wa.me/6282340188130" class="whatsapp-button mt-3 mt-md-0">
            <i class="fab fa-whatsapp"></i> Hubungi Admin
        </a>
    </div>
</div>

{{-- Popular Trips --}}
<h2 class="mb-4"><i class="fas fa-tree text-primary"></i> Destinasi Populer</h2>
<div class="row g-4">
    @foreach($trips as $trip)
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-primary">{{ $trip->nama }}</h5>
                <p class="card-text text-muted">
                    <i class="fas fa-map-marker-alt"></i> {{ $trip->lokasi }}<br>
                    <i class="fas fa-clock"></i> {{ $trip->durasi }} hari
                </p>
                <p class="fw-bold text-success">Rp {{ number_format($trip->harga, 0, ',', '.') }} <span class="fw-normal text-muted">/ orang</span></p>
                <p class="small text-secondary">{{ Str::limit($trip->deskripsi, 80) }}</p>
                <a href="{{ route('transaksi') }}?trip={{ $trip->id }}" class="btn btn-primary w-100 mt-2">
                    <i class="fas fa-shopping-cart"></i> Pesan
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Recent Bookings --}}
@if($myBookings->count() > 0)
<h2 class="mt-5 mb-4"><i class="fas fa-history text-primary"></i> Booking Terbaru Kamu</h2>
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Paket Trip</th>
                <th>Tgl Berangkat</th>
                <th>Peserta</th>
                <th>Total Harga</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($myBookings as $booking)
            <tr>
                <td>{{ $booking->trip->nama }}<br><small class="text-muted">{{ $booking->trip->lokasi }}</small></td>
                <td>{{ $booking->tanggal_berangkat->format('d/m/Y') }}</td>
                <td>{{ $booking->jumlah_peserta }} orang</td>
                <td>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                <td>
                    @php
                        $statusClass = match($booking->status) {
                            'pending' => 'bg-warning',
                            'dikonfirmasi' => 'bg-info',
                            'selesai' => 'bg-success',
                            'batal' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ $booking->status }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<script>
    async function fetchWeather() {
        const loading = document.getElementById('weatherLoading');
        const content = document.getElementById('weatherContent');

        try {
            const response = await fetch('/api/weather');
            const data = await response.json();

            if (data.success) {
                content.innerHTML = `
                    <div class="row text-center">
                        <div class="col-md-6">
                            <div class="p-4 border rounded-3 bg-light">
                                <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                                <h4 class="fw-bold">${data.banyuwangi.city}</h4>
                                <div class="display-5 fw-bold text-primary">${data.banyuwangi.temperature}°C</div>
                                <p class="mt-2"><i class="fas fa-cloud-rain"></i> ${data.banyuwangi.description}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-4 border rounded-3 bg-light">
                                <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                                <h4 class="fw-bold">${data.bali.city}</h4>
                                <div class="display-5 fw-bold text-primary">${data.bali.temperature}°C</div>
                                <p class="mt-2"><i class="fas fa-cloud-sun"></i> ${data.bali.description}</p>
                            </div>
                        </div>
                    </div>
                `;
                loading.style.display = 'none';
                content.style.display = 'block';
            }
        } catch (error) {
            loading.innerHTML = '<p class="text-danger text-center">Gagal memuat data cuaca</p>';
        }
    }

    fetchWeather();
    setInterval(fetchWeather, 600000);
</script>
@endsection
