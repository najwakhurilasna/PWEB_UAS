@extends('layouts.guest')

@section('title', 'Lupa Password - NajaTrip')

@section('content')
<div class="text-center mb-4">
    <h4><i class="fas fa-key"></i> Lupa Password</h4>
    <p class="text-muted">Masukkan email dan password baru Anda</p>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('lupa.password.post') }}">
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror"
               id="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password Baru</label>
        <input type="password" class="form-control @error('password') is-invalid @enderror"
               id="password" name="password" required>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
        <input type="password" class="form-control"
               id="password_confirmation" name="password_confirmation" required>
    </div>

    <div class="alert alert-info small mb-3">
        <i class="fas fa-info-circle"></i> Password minimal 4 karakter
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2">
        <i class="fas fa-save"></i> Ganti Password
    </button>

    <div class="text-center mt-3">
        <a href="{{ route('login') }}" class="text-muted">
            <i class="fas fa-arrow-left"></i> Kembali ke Login
        </a>
    </div>
</form>
@endsection
