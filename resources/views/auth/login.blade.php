@extends('layouts.guest')

@section('title', 'Login - NajaTrip')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="form-group">
        <label><i class="fas fa-envelope"></i> Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label><i class="fas fa-lock"></i> Password</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="remember" id="remember">
        <label class="form-check-label" for="remember">
            <i class="fas fa-check-circle"></i> Remember me
        </label>
    </div>

    {{-- TOMBOL LUPA PASSWORD (menggunakan route yang benar) --}}
    <div class="text-end mb-3">
        <a href="{{ route('lupa.password') }}" class="text-muted small">
            <i class="fas fa-question-circle"></i> Lupa Password?
        </a>
    </div>

    <button type="submit" class="btn-auth w-100 py-2">
        <i class="fas fa-sign-in-alt"></i> LOG IN
    </button>

    <div class="text-center mt-3">
        <a href="{{ route('register') }}" class="text-muted">
            <i class="fas fa-user-plus"></i> Don't have account? Register
        </a>
    </div>
</form>
@endsection
