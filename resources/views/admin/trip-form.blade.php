@extends('layouts.app')

@section('title', $trip ? 'Edit Trip' : 'Tambah Trip')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header {{ $trip ? 'bg-warning' : 'bg-primary' }} text-white">
                <h4 class="mb-0">
                    <i class="fas {{ $trip ? 'fa-edit' : 'fa-plus' }}"></i>
                    {{ $trip ? 'Edit Paket Trip' : 'Tambah Paket Trip' }}
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ $trip ? route('admin.trip.update', $trip->id) : route('admin.trip.store') }}" method="POST">
                    @csrf
                    @if($trip)
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Nama Trip</label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama', $trip->nama ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <select name="lokasi" class="form-select" required>
                            <option value="Banyuwangi" {{ old('lokasi', $trip->lokasi ?? '') == 'Banyuwangi' ? 'selected' : '' }}>Banyuwangi</option>
                            <option value="Bali" {{ old('lokasi', $trip->lokasi ?? '') == 'Bali' ? 'selected' : '' }}>Bali</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="5" required>{{ old('deskripsi', $trip->deskripsi ?? '') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga (Rp)</label>
                            <input type="number" name="harga" class="form-control" value="{{ old('harga', $trip->harga ?? '') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Durasi (hari)</label>
                            <input type="number" name="durasi" class="form-control" value="{{ old('durasi', $trip->durasi ?? '') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fasilitas (pisahkan dengan koma)</label>
                        @php
                            $fasilitasStr = '';
                            if(isset($trip) && $trip->fasilitas) {
                                $fasilitas = is_array($trip->fasilitas) ? $trip->fasilitas : json_decode($trip->fasilitas, true) ?? [];
                                $fasilitasStr = implode(', ', $fasilitas);
                            }
                        @endphp
                        <input type="text" name="fasilitas" class="form-control" value="{{ old('fasilitas', $fasilitasStr) }}" placeholder="Contoh: Transportasi, Makan, Guide">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="aktif" {{ old('status', $trip->status ?? '') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status', $trip->status ?? '') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ $trip ? 'Update' : 'Simpan' }}
                        </button>
                        <a href="{{ route('detail') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
