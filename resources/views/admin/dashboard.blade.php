@extends('layouts.app')

@section('title', 'Admin Dashboard - NajaTrip')

@section('content')
<div class="hero-gradient text-white p-4 rounded-4 mb-4">
    <h1 class="display-5 fw-bold">Dashboard Admin</h1>
    <p class="lead">Selamat datang, {{ Auth::user()->name }}!</p>
</div>

<div class="row g-4 mb-5">
    {{-- Total Trip --}}
    <div class="col-md-3">
        <div class="card bg-primary text-white text-center">
            <div class="card-body">
                <i class="fas fa-route fa-3x mb-2"></i>
                <h2 class="fw-bold">{{ $totalTrips }}</h2>
                <p>Total Trip</p>
            </div>
        </div>
    </div>

    {{-- Total Booking --}}
    <div class="col-md-3">
        <div class="card bg-info text-white text-center">
            <div class="card-body">
                <i class="fas fa-clipboard-list fa-3x mb-2"></i>
                <h2 class="fw-bold">{{ $totalBookings }}</h2>
                <p>Total Booking</p>
            </div>
        </div>
    </div>

    {{-- Trip Selesai --}}
    <div class="col-md-3">
        <div class="card bg-success text-white text-center">
            <div class="card-body">
                <i class="fas fa-check-circle fa-3x mb-2"></i>
                <h2 class="fw-bold">{{ $completedBookings }}</h2>
                <p>Trip Selesai</p>
            </div>
        </div>
    </div>

    {{-- Penghasilan --}}
    <div class="col-md-3">
        <div class="card bg-warning text-white text-center">
            <div class="card-body">
                <i class="fas fa-money-bill-wave fa-3x mb-2"></i>
                <h2 class="fw-bold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
                <p>Penghasilan</p>
            </div>
        </div>
    </div>
</div>

{{-- Weather Widget --}}
<div class="card shadow-sm mb-5" id="weatherWidget">
    <div class="card-body text-center py-5" id="weatherLoading">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-2 mb-0">Memuat data cuaca...</p>
    </div>
    <div id="weatherContent" class="card-body" style="display:none;"></div>
</div>

{{-- Daftar Trip --}}
<h2 class="mb-4"><i class="fas fa-route text-primary"></i> Daftar Trip</h2>
<div class="row g-4 mb-5">
    @foreach($trips as $trip)
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">{{ $trip->nama }}</h5>
                <p class="card-text text-muted">
                    <i class="fas fa-map-marker-alt"></i> {{ $trip->lokasi }}
                </p>
                <p class="fw-bold text-primary">Rp {{ number_format($trip->harga, 0, ',', '.') }}</p>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.trip.edit', $trip->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('admin.trip.destroy', $trip->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 bg-light">
            <div class="card-body text-center">
                <a href="{{ route('admin.trip.create') }}" class="btn btn-outline-primary">
                    <i class="fas fa-plus"></i> Tambah Trip
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Recent Bookings --}}
<h2 class="mb-4"><i class="fas fa-clock text-primary"></i> Booking Terbaru</h2>
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Customer</th>
                <th>Paket</th>
                <th>Tgl Berangkat</th>
                <th>Peserta</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentBookings as $booking)
            <tr>
                <td>{{ $booking->user->name }}<br><small class="text-muted">{{ $booking->user->email }}</small></td>
                <td>{{ $booking->trip->nama }}<br><small>{{ $booking->trip->lokasi }}</small></td>
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
