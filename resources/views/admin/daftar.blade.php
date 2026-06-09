@extends('layouts.app')

@section('title', 'Daftar Pesanan - Admin')

@section('content')
<div class="hero-gradient text-white p-4 rounded-4 mb-4">
    <h1 class="display-6 fw-bold">Daftar Semua Pesanan</h1>
    <p>Kelola status pemesanan customer (pending, dikonfirmasi, selesai, batal)</p>
</div>

{{-- Filter Status --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-3">
                <label class="form-label fw-bold">Filter Status:</label>
            </div>
            <div class="col-md-6">
                <select id="statusFilter" class="form-select">
                    <option value="all">Semua Status</option>
                    <option value="pending">⏳ Pending</option>
                    <option value="dikonfirmasi">✅ Dikonfirmasi</option>
                    <option value="selesai">🎉 Selesai</option>
                    <option value="batal">❌ Batal</option>
                </select>
            </div>
            <div class="col-md-3">
                <button onclick="resetFilter()" class="btn btn-secondary w-100">
                    <i class="fas fa-sync-alt"></i> Reset Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Customer</th>
                <th>Paket Trip</th>
                <th>Tgl Berangkat</th>
                <th>Peserta</th>
                <th>Total Harga</th>
                <th>Status</th>
                <th>Dokumen</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $key => $booking)
            <tr class="booking-row" data-status="{{ $booking->status }}">
                <td>{{ $key + 1 }}</td>
                <td>
                    <strong>{{ $booking->nama_lengkap }}</strong><br>
                    <small class="text-muted">{{ $booking->user->email }}</small><br>
                    <small><i class="fas fa-phone"></i> {{ $booking->nomor_telepon }}</small>
                </td>
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
                    <div class="d-flex flex-column gap-2">
                        <form action="{{ route('admin.booking.status', $booking->id) }}" method="POST" class="d-flex gap-2">
                            @csrf
                            @method('PUT')
                            <select name="status" class="form-select form-select-sm" style="width: 140px;">
                                <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="dikonfirmasi" {{ $booking->status == 'dikonfirmasi' ? 'selected' : '' }}>✅ Dikonfirmasi</option>
                                <option value="selesai" {{ $booking->status == 'selesai' ? 'selected' : '' }}>🎉 Selesai</option>
                                <option value="batal" {{ $booking->status == 'batal' ? 'selected' : '' }}>❌ Batal</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Ubah status booking ini?')">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </form>
                        <a href="https://wa.me/62{{ preg_replace('/[^0-9]/', '', $booking->nomor_telepon) }}" target="_blank" class="btn btn-success btn-sm">
                            <i class="fab fa-whatsapp"></i> Hubungi
                        </a>
                    </div>
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                        Belum ada pesanan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    // Filter status berdasarkan dropdown
    const statusFilter = document.getElementById('statusFilter');
    const rows = document.querySelectorAll('.booking-row');

    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            const selectedStatus = this.value;

            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                if (selectedStatus === 'all' || rowStatus === selectedStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    function resetFilter() {
        if (statusFilter) {
            statusFilter.value = 'all';
            rows.forEach(row => {
                row.style.display = '';
            });
        }
    }
</script>
@endsection
