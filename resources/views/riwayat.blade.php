@extends('layouts.app')

@section('title', 'Riwayat Pemesanan - NajaTrip')

@section('content')
<div class="hero-gradient text-white p-4 rounded-4 mb-4">
    <h1 class="display-6 fw-bold">Riwayat Pemesanan</h1>
    <p>Daftar semua transaksi Anda</p>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Paket Trip</th>
                <th>Tanggal Berangkat</th>
                <th>Peserta</th>
                <th>Total Harga</th>
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
                        $statusText = match($booking->status) {
                            'pending' => '⏳ Pending',
                            'dikonfirmasi' => '✅ Dikonfirmasi',
                            'selesai' => '🎉 Selesai',
                            'batal' => '❌ Batal',
                            default => $booking->status
                        };
                    @endphp
                    <span class="badge {{ $statusClass }} px-3 py-2">{{ $statusText }}</span>
                </td>
                <td>
                    <div class="d-flex gap-2">
                        @if($booking->ktp_path)
                            <a href="{{ asset('storage/' . $booking->ktp_path) }}" target="_blank" class="btn btn-sm btn-info" title="Lihat KTP">
                                <i class="fas fa-id-card"></i> KTP
                            </a>
                        @endif
                        @if($booking->bukti_path)
                            <a href="{{ asset('storage/' . $booking->bukti_path) }}" target="_blank" class="btn btn-sm btn-info" title="Lihat Bukti Transfer">
                                <i class="fas fa-receipt"></i> Bukti
                            </a>
                        @endif
                    </div>
                </td>
                <td>
                    @if($booking->status == 'pending')
                        <button type="button" class="btn btn-danger btn-sm" onclick="cancelBooking({{ $booking->id }})">
                            <i class="fas fa-times"></i> Batalkan
                        </button>
                    @elseif($booking->status == 'dikonfirmasi')
                        <span class="text-info">Menunggu keberangkatan</span>
                    @elseif($booking->status == 'selesai')
                        <span class="text-success">Selesai</span>
                    @elseif($booking->status == 'batal')
                        <span class="text-danger">Dibatalkan</span>
                    @endif
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                        Belum ada transaksi
                        <div class="mt-3">
                            <a href="{{ route('transaksi') }}" class="btn btn-primary">Booking Sekarang</a>
                        </div>
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
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }
    }
</script>
@endsection
