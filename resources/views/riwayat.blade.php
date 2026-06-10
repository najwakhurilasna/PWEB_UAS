@extends('layouts.app')

@section('title', 'Riwayat Pemesanan - NajaTrip')

@section('content')
<div class="hero-gradient text-white p-4 rounded-4 mb-4">
    <h1 class="display-6 fw-bold">Riwayat Pemesanan</h1>
    <p class="mb-0">Daftar semua transaksi Anda</p>
</div>

{{-- Statistik ringkas --}}
@if($totalBookings > 0)
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center border-0 bg-light">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-dark">{{ $totalBookings }}</div>
                <small class="text-muted">Total Booking</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center border-0" style="background:#fff8e1;">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-warning">{{ $pendingCount }}</div>
                <small class="text-muted">Pending</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center border-0" style="background:#e8f5e9;">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-success">{{ $selesaiCount }}</div>
                <small class="text-muted">Selesai</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center border-0" style="background:#e3f2fd;">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-primary">Rp {{ number_format($totalSpent, 0, ',', '.') }}</div>
                <small class="text-muted">Total Selesai</small>
            </div>
        </div>
    </div>
</div>
@endif

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Paket Trip</th>
                <th>Tanggal Berangkat</th>
                <th>Peserta</th>
                <th>Total Harga</th>
                <th>Catatan</th>
                <th>Status</th>
                <th>Dokumen</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $key => $booking)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>
                    <strong>{{ $booking->trip->nama }}</strong><br>
                    <small class="text-muted">{{ $booking->trip->lokasi }}</small>
                </td>
                <td>{{ $booking->tanggal_berangkat->format('d/m/Y') }}</td>
                <td class="text-center">{{ $booking->jumlah_peserta }} orang</td>
                <td>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                <td>
                    @if($booking->catatan)
                        <span class="text-dark" style="white-space: pre-line;">{{ $booking->catatan }}</span>
                    @else
                        <span class="text-muted fst-italic small">-</span>
                    @endif
                </td>
                <td>
                    @php
                        $statusClass = match($booking->status) {
                            'pending'      => 'bg-warning text-dark',
                            'dikonfirmasi' => 'bg-info text-white',
                            'selesai'      => 'bg-success text-white',
                            'batal'        => 'bg-danger text-white',
                            default        => 'bg-secondary text-white'
                        };
                        $statusText = match($booking->status) {
                            'pending'      => '⏳ Pending',
                            'dikonfirmasi' => '✅ Dikonfirmasi',
                            'selesai'      => '🎉 Selesai',
                            'batal'        => '❌ Batal',
                            default        => $booking->status
                        };
                    @endphp
                    <span class="badge {{ $statusClass }} px-3 py-2">{{ $statusText }}</span>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        @if($booking->ktp_path)
                            <a href="{{ asset('storage/' . $booking->ktp_path) }}" target="_blank"
                               class="btn btn-sm btn-outline-info" title="Lihat KTP">
                                <i class="fas fa-id-card"></i> KTP
                            </a>
                        @endif
                        @if($booking->bukti_path)
                            <a href="{{ asset('storage/' . $booking->bukti_path) }}" target="_blank"
                               class="btn btn-sm btn-outline-info" title="Lihat Bukti Transfer">
                                <i class="fas fa-receipt"></i> Bukti
                            </a>
                        @endif
                    </div>
                </td>
                <td>
                    @if($booking->status == 'pending')
                        <button type="button" class="btn btn-danger btn-sm"
                                onclick="cancelBooking({{ $booking->id }})">
                            <i class="fas fa-times"></i> Batalkan
                        </button>
                    @elseif($booking->status == 'dikonfirmasi')
                        <span class="text-info small"><i class="fas fa-clock"></i> Menunggu keberangkatan</span>
                    @elseif($booking->status == 'selesai')
                        <span class="text-success small"><i class="fas fa-check"></i> Selesai</span>
                    @elseif($booking->status == 'batal')
                        <span class="text-danger small"><i class="fas fa-ban"></i> Dibatalkan</span>
                    @endif
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted">Belum ada transaksi</p>
                        <a href="{{ route('transaksi') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-cart"></i> Booking Sekarang
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    function cancelBooking(bookingId) {
        if (confirm('Yakin ingin membatalkan booking ini?')) {
            fetch(`/booking/${bookingId}/cancel`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Booking berhasil dibatalkan!');
                    location.reload();
                } else {
                    alert('Gagal membatalkan: ' + data.message);
                }
            })
            .catch(error => alert('Error: ' + error.message));
        }
    }
</script>
@endsection
