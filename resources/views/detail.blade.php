@extends('layouts.app')

@section('title', 'Detail Trip - NajaTrip')

@section('content')
<div class="hero-gradient text-white p-5 rounded-4 mb-4 text-center">
    <h1 class="display-5 fw-bold">Daftar Paket Trip</h1>
    <p class="lead">Pilih destinasi favoritmu dan mulai petualangan!</p>

    {{-- Tombol Tambah Trip (untuk admin) --}}
    @auth
        @if(Auth::user()->isAdmin())
            <a href="{{ route('admin.trip.create') }}" class="btn btn-success mt-3">
                <i class="fas fa-plus"></i> Tambah Trip Baru
            </a>
        @endif
    @endauth

    <div class="row justify-content-center mt-3">
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" id="searchInput" class="form-control form-control-lg" placeholder="Cari trip...">
                <span class="input-group-text bg-white"><i class="fas fa-search text-primary"></i></span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4" id="tripContainer">
    @foreach($trips as $trip)
    <div class="col-md-6 col-lg-4 trip-card" data-nama="{{ strtolower($trip->nama) }}" data-lokasi="{{ strtolower($trip->lokasi) }}">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <h5 class="card-title text-primary fw-bold">{{ $trip->nama }}</h5>
                    <span class="badge bg-secondary">{{ $trip->lokasi }}</span>
                </div>
                <p class="card-text text-muted mt-2">
                    <i class="fas fa-clock"></i> {{ $trip->durasi }} hari
                </p>
                <p class="fw-bold text-success fs-4">Rp {{ number_format($trip->harga, 0, ',', '.') }}</p>
                <p class="small text-secondary">{{ Str::limit($trip->deskripsi, 100) }}</p>

                @php
                    $fasilitas = is_array($trip->fasilitas) ? $trip->fasilitas : json_decode($trip->fasilitas, true) ?? [];
                @endphp
                @if(!empty($fasilitas))
                <div class="mb-3">
                    <small class="text-muted">Fasilitas:</small>
                    <div class="d-flex flex-wrap gap-1 mt-1">
                        @foreach(array_slice($fasilitas, 0, 3) as $f)
                        <span class="badge bg-light text-dark border">✓ {{ $f }}</span>
                        @endforeach
                        @if(count($fasilitas) > 3)
                        <span class="badge bg-secondary">+{{ count($fasilitas) - 3 }}</span>
                        @endif
                    </div>
                </div>
                @endif

                <a href="{{ route('transaksi') }}?trip={{ $trip->id }}" class="btn btn-primary w-100 mt-2">
                    <i class="fas fa-shopping-cart"></i> Pesan Sekarang
                </a>

                {{-- Tombol Edit & Hapus untuk Admin --}}
                @auth
                    @if(Auth::user()->isAdmin())
                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('admin.trip.edit', $trip->id) }}" class="btn btn-warning btn-sm w-50">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.trip.destroy', $trip->id) }}" method="POST" class="w-50">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Yakin hapus trip ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>
    @endforeach
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const cards = document.querySelectorAll('.trip-card');

    searchInput.addEventListener('input', function() {
        const keyword = this.value.toLowerCase().trim();

        cards.forEach(card => {
            const nama = card.getAttribute('data-nama');
            const lokasi = card.getAttribute('data-lokasi');

            if (keyword === '' || nama.includes(keyword) || lokasi.includes(keyword)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>
@endsection
