@extends('layouts.app')

@section('title', 'Edit Informasi Transaksi - Admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-edit"></i> Edit Informasi Transaksi</h4>
                <small>Edit konten yang tampil di halaman Transaksi (Customer)</small>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.update-info-transaksi') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-bold">💳 Informasi Pembayaran</label>
                        <textarea name="payment_info" class="form-control" rows="4">{{ $info['payment_info'] }}</textarea>
                        <small class="text-muted">Gunakan &lt;br&gt; untuk baris baru</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">📋 Syarat & Ketentuan</label>
                        <textarea name="terms_conditions" class="form-control" rows="5">{{ $info['terms_conditions'] }}</textarea>
                        <small class="text-muted">Gunakan &lt;br&gt; untuk baris baru</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">📊 Informasi Kuota</label>
                        <textarea name="quota_info" class="form-control" rows="3">{{ $info['quota_info'] }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">📞 Nomor WhatsApp (tanpa 62/0)</label>
                        <input type="text" name="whatsapp_number" class="form-control" value="{{ $info['whatsapp_number'] }}">
                        <small class="text-muted">Contoh: 6282340188130</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">💬 Teks Tombol WhatsApp</label>
                        <input type="text" name="contact_text" class="form-control" value="{{ $info['contact_text'] }}">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('transaksi') }}" class="btn btn-secondary">
                            <i class="fas fa-eye"></i> Lihat Halaman Transaksi
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
