@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="form-group">
        <label><i class="fas fa-user"></i> Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
        @error('name')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label><i class="fas fa-envelope"></i> Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        @error('email')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label><i class="fas fa-lock"></i> Password</label>
        <input type="password" name="password" class="form-control" required>
        @error('password')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label><i class="fas fa-check-circle"></i> Confirm Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>

    <button type="submit" class="btn-auth">
        <i class="fas fa-user-plus"></i> REGISTER
    </button>

    <div class="auth-link">
        <a href="{{ route('login') }}">Already have account? Login</a>
    </div>
</form>
@endsection
