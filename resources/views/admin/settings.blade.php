@extends('layouts.app')

@section('title', 'Pengaturan Website - Admin')

@section('content')
<div class="hero-gradient text-white p-4 rounded-4 mb-4">
    <h1 class="display-6 fw-bold">Pengaturan Website</h1>
    <p>Edit informasi yang tampil di halaman Transaksi (customer)</p>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Informasi Pemesanan</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf

                    {{-- Payment Info --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">💳 Informasi Pembayaran</label>
                        @php
                            $paymentInfo = $settings->where('key', 'payment_info')->first();
                        @endphp
                        <textarea name="settings[payment_info]" class="form-control" rows="4">{{ old('settings.payment_info', $paymentInfo->value ?? '') }}</textarea>
                        <small class="text-muted">Gunakan &lt;br&gt; untuk baris baru</small>
                    </div>

                    {{-- Terms & Conditions --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">📋 Syarat & Ketentuan</label>
                        @php
                            $terms = $settings->where('key', 'terms_conditions')->first();
                        @endphp
                        <textarea name="settings[terms_conditions]" class="form-control" rows="5">{{ old('settings.terms_conditions', $terms->value ?? '') }}</textarea>
                        <small class="text-muted">Gunakan &lt;br&gt; untuk baris baru</small>
                    </div>

                    {{-- Quota Info --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">📊 Informasi Kuota</label>
                        @php
                            $quotaInfo = $settings->where('key', 'quota_info')->first();
                        @endphp
                        <textarea name="settings[quota_info]" class="form-control" rows="3">{{ old('settings.quota_info', $quotaInfo->value ?? '') }}</textarea>
                    </div>

                    {{-- WhatsApp Number --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">📞 Nomor WhatsApp (tanpa 62/0)</label>
                        @php
                            $waNumber = $settings->where('key', 'whatsapp_number')->first();
                        @endphp
                        <input type="text" name="settings[whatsapp_number]" class="form-control" value="{{ old('settings.whatsapp_number', $waNumber->value ?? '6282340188130') }}">
                        <small class="text-muted">Contoh: 6282340188130</small>
                    </div>

                    {{-- Contact Text --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">💬 Teks Tombol Kontak</label>
                        @php
                            $contactText = $settings->where('key', 'contact_text')->first();
                        @endphp
                        <input type="text" name="settings[contact_text]" class="form-control" value="{{ old('settings.contact_text', $contactText->value ?? 'Butuh bantuan? Hubungi Admin') }}">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Semua Pengaturan
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-eye"></i> Preview Informasi (Customer View)</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="border p-3 rounded">
                            <h6 class="fw-bold">💳 Pembayaran</h6>
                            <p>{!! nl2br(e($paymentInfo->value ?? '')) !!}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border p-3 rounded">
                            <h6 class="fw-bold">📋 Syarat & Ketentuan</h6>
                            <p>{!! nl2br(e($terms->value ?? '')) !!}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
