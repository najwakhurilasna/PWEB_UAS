@extends('layouts.app')

@section('title', 'Form Pemesanan - NajaTrip')

@section('content')
<div class="row">

    {{-- ===================== FORM PEMESANAN ===================== --}}
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-clipboard-list"></i> Form Pemesanan Trip</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('transaksi.store') }}" method="POST" enctype="multipart/form-data" id="bookingForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control"
                                value="{{ old('nama_lengkap', Auth::user()->name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="tel" name="nomor_telepon" class="form-control"
                                placeholder="08xxxxxxxxxx" value="{{ old('nomor_telepon') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pilih Paket Trip <span class="text-danger">*</span></label>
                            <select name="trip_id" id="tripSelect" class="form-select" required>
                                <option value="">-- Pilih Paket Trip --</option>
                                @foreach($trips as $trip)
                                <option value="{{ $trip->id }}" data-harga="{{ $trip->harga }}">
                                    {{ $trip->nama }} - {{ $trip->lokasi }} (Rp {{ number_format($trip->harga, 0, ',', '.') }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Keberangkatan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_berangkat" id="tanggalBerangkat" class="form-control"
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                            <div id="quotaInfo" class="small mt-1"></div>
                            <div id="tripSlotInfo" class="small mt-1"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jumlah Peserta <span class="text-danger">*</span> (Max 8 orang)</label>
                            <input type="number" name="jumlah_peserta" id="jumlahPeserta"
                                class="form-control" min="1" max="8" value="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total Harga</label>
                            <h3 id="totalHargaDisplay" class="text-primary mb-0">Rp 0</h3>
                            <input type="hidden" name="total_harga" id="totalHargaInput">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Upload KTP/KK <span class="text-danger">*</span></label>
                            <input type="file" name="ktp" class="form-control" accept="image/*" required>
                            <small class="text-muted">Format: JPG, PNG. Max 2MB</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Upload Bukti Transfer DP 50% <span class="text-danger">*</span></label>
                            <input type="file" name="bukti" class="form-control" accept="image/*" required>
                            <small class="text-muted">Format: JPG, PNG. Max 2MB</small>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Catatan Tambahan (opsional)</label>
                            <textarea name="catatan" class="form-control" rows="3"
                                placeholder="Contoh: request penjemputan, diet khusus, dll">{{ old('catatan') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100 py-2" id="submitBtn">
                                <i class="fas fa-paper-plane"></i> Booking Sekarang
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===================== PANEL KANAN ===================== --}}
    <div class="col-md-4">

        {{-- Tombol Edit — HANYA ADMIN, pakai data-bs-toggle supaya pasti jalan --}}
        @auth
        @if(Auth::user()->isAdmin())
        <div class="mb-3">
            <button type="button"
                    class="btn btn-warning w-100 fw-bold"
                    data-bs-toggle="modal"
                    data-bs-target="#editInfoModal">
                <i class="fas fa-edit me-1"></i> Edit Informasi Transaksi
            </button>
        </div>
        @endif
        @endauth

        {{-- Kartu Informasi Pemesanan --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header text-white" style="background:#0284c7;">
                <h5 class="mb-0"><i class="fas fa-info-circle me-1"></i> Informasi Pemesanan</h5>
            </div>
            <div class="card-body p-0">

                {{-- Rekening Pembayaran --}}
                <div class="p-3 border-bottom" style="background:#f0f9ff;">
                    <p class="fw-bold mb-2 text-primary">
                        <i class="fas fa-university me-1"></i> Rekening Pembayaran (DP 50%)
                    </p>
                    <div id="rekeningDisplay">
                        <table class="table table-sm table-borderless mb-0" style="font-size:0.95rem;">
                            <tr>
                                <td class="text-muted py-1" style="width:80px;">Bank</td>
                                <td class="py-1">: <strong id="dispBankName">{{ $bankName ?? 'BRI' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted py-1">No. Rek</td>
                                <td class="py-1">: <strong id="dispBankNumber" style="letter-spacing:1px;">{{ $bankNumber ?? '1234567890' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted py-1">a.n.</td>
                                <td class="py-1">: <span id="dispBankOwner">{{ $bankOwner ?? 'Admin NajaTrip' }}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Cara Pembayaran --}}
                <div class="p-3 border-bottom">
                    <p class="fw-bold mb-1">
                        <i class="fas fa-credit-card text-success me-1"></i> Cara Pembayaran
                    </p>
                    <div class="small text-muted" id="paymentInfoDisplay">
                        {!! $paymentInfo ?? 'DP 50% dari total harga<br>Sisa pembayaran dapat diinformasikan via WhatsApp<br>Admin akan menghubungi Anda setelah booking dikonfirmasi' !!}
                    </div>
                </div>

                {{-- Syarat & Ketentuan --}}
                <div class="p-3 border-bottom">
                    <p class="fw-bold mb-1">
                        <i class="fas fa-file-contract text-warning me-1"></i> Syarat & Ketentuan
                    </p>
                    <div class="small text-muted" id="termsDisplay">
                        {!! $termsConditions ?? 'Booking dianggap sah setelah upload bukti transfer<br>Pembatalan H-7: refund 50%<br>Pembatalan H-3: refund 25%<br>Pembatalan H-1: tidak ada refund' !!}
                    </div>
                </div>

                {{-- Kontak --}}
                <div class="p-3">
                    <p class="fw-bold mb-2">
                        <i class="fas fa-headset text-primary me-1"></i>
                        <span id="contactTextDisplay">{{ $contactText ?? 'Butuh bantuan? Hubungi Admin' }}</span>
                    </p>
                    <a href="https://wa.me/{{ $whatsappNumber ?? '6282340188130' }}"
                       id="whatsappLink"
                       class="whatsapp-button w-100 justify-content-center"
                       target="_blank">
                        <i class="fab fa-whatsapp"></i> Chat Admin via WhatsApp
                    </a>
                </div>

            </div>
        </div>

        {{-- Info Kuota --}}
        <div class="card shadow-sm">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="fas fa-users me-1"></i> Informasi Kuota</h5>
            </div>
            <div class="card-body">
                <div class="small">
                    <p class="mb-1">✅ <strong>Max 8 orang</strong> per trip per tanggal</p>
                    <p class="mb-1">✅ <strong>Max 2 trip berbeda</strong> per tanggal</p>
                    <p class="mb-0">⚠️ Jika kuota penuh, booking tidak dapat diproses</p>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ===================== MODAL EDIT (ADMIN ONLY) ===================== --}}
@auth
@if(Auth::user()->isAdmin())
<div class="modal fade" id="editInfoModal" tabindex="-1" aria-labelledby="editInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header" style="background:#f59e0b;">
                <h5 class="modal-title fw-bold" id="editInfoModalLabel">
                    <i class="fas fa-edit me-1"></i> Edit Informasi Transaksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="editInfoForm">
                    @csrf

                    {{-- Rekening --}}
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header text-white fw-bold" style="background:#1e40af;">
                            <i class="fas fa-university me-1"></i> Rekening Pembayaran
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Bank</label>
                                <input type="text" name="bank_name" id="bankNameInput" class="form-control"
                                    placeholder="Contoh: BRI / BCA / Mandiri / BSI"
                                    value="{{ session('transaksi_info.bank_name', 'BRI') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nomor Rekening</label>
                                <input type="text" name="bank_number" id="bankNumberInput" class="form-control"
                                    placeholder="Contoh: 1234567890"
                                    value="{{ session('transaksi_info.bank_number', '1234567890') }}">
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-semibold">Atas Nama</label>
                                <input type="text" name="bank_owner" id="bankOwnerInput" class="form-control"
                                    placeholder="Contoh: Najwa Salsabila"
                                    value="{{ session('transaksi_info.bank_owner', 'Admin NajaTrip') }}">
                            </div>
                        </div>
                    </div>

                    {{-- Cara Pembayaran --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-credit-card text-success me-1"></i> Cara Pembayaran
                        </label>
                        <textarea name="payment_info" id="paymentInfoInput" class="form-control" rows="4"
                            >{{ session('transaksi_info.payment_info', 'DP 50% dari total harga<br>Sisa pembayaran dapat diinformasikan via WhatsApp<br>Admin akan menghubungi Anda setelah booking dikonfirmasi') }}</textarea>
                        <small class="text-muted">Gunakan &lt;br&gt; untuk baris baru</small>
                    </div>

                    {{-- Syarat & Ketentuan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-file-contract text-warning me-1"></i> Syarat & Ketentuan
                        </label>
                        <textarea name="terms_conditions" id="termsInput" class="form-control" rows="5"
                            >{{ session('transaksi_info.terms_conditions', 'Booking dianggap sah setelah upload bukti transfer<br>Pembatalan H-7: refund 50%<br>Pembatalan H-3: refund 25%<br>Pembatalan H-1: tidak ada refund') }}</textarea>
                        <small class="text-muted">Gunakan &lt;br&gt; untuk baris baru</small>
                    </div>

                    {{-- WhatsApp --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fab fa-whatsapp text-success me-1"></i> Nomor WhatsApp Admin
                        </label>
                        <input type="text" name="whatsapp_number" id="whatsappInput" class="form-control"
                            placeholder="Contoh: 6282340188130"
                            value="{{ session('transaksi_info.whatsapp_number', '6282340188130') }}">
                        <small class="text-muted">Format: 62xxxxxxxxxx (tanpa tanda + atau 0 di depan)</small>
                    </div>

                    {{-- Teks Kontak --}}
                    <div class="mb-0">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-comment text-info me-1"></i> Teks Label Kontak
                        </label>
                        <input type="text" name="contact_text" id="contactTextInput" class="form-control"
                            placeholder="Contoh: Butuh bantuan? Hubungi Admin"
                            value="{{ session('transaksi_info.contact_text', 'Butuh bantuan? Hubungi Admin') }}">
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-primary" id="btnSimpanInfo">
                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                </button>
            </div>

        </div>
    </div>
</div>
@endif
@endauth

<script>
// ======================== KALKULASI HARGA ========================
const tripSelect       = document.getElementById('tripSelect');
const jumlahPeserta    = document.getElementById('jumlahPeserta');
const totalDisplay     = document.getElementById('totalHargaDisplay');
const totalInput       = document.getElementById('totalHargaInput');
const tanggalBerangkat = document.getElementById('tanggalBerangkat');
const quotaInfoDiv     = document.getElementById('quotaInfo');
const tripSlotInfoDiv  = document.getElementById('tripSlotInfo');
const submitBtn        = document.getElementById('submitBtn');
let isSubmitting = false;

function updateTotal() {
    const selected = tripSelect.options[tripSelect.selectedIndex];
    const harga    = selected ? (selected.dataset.harga || 0) : 0;
    const peserta  = parseInt(jumlahPeserta.value) || 1;
    const total    = harga * peserta;
    totalDisplay.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    totalInput.value = total;
}

async function checkQuota() {
    const tripId  = tripSelect.value;
    const tanggal = tanggalBerangkat.value;
    if (!tripId || !tanggal) {
        quotaInfoDiv.innerHTML = '';
        tripSlotInfoDiv.innerHTML = '';
        submitBtn.disabled = false;
        return;
    }
    try {
        const res  = await fetch(`/api/check-quota?trip_id=${tripId}&tanggal=${tanggal}`);
        const data = await res.json();

        if (data.is_quota_full) {
            quotaInfoDiv.innerHTML = `<span class="text-danger"><i class="fas fa-times-circle"></i> Kuota trip ini sudah penuh! (${data.total_terpakai}/${data.max_kuota} orang)</span>`;
            submitBtn.disabled = true;
        } else {
            const peserta = parseInt(jumlahPeserta.value) || 1;
            if (peserta > data.sisa_kuota) {
                quotaInfoDiv.innerHTML = `<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Jumlah peserta (${peserta}) melebihi sisa kuota (${data.sisa_kuota})!</span>`;
                submitBtn.disabled = true;
            } else {
                quotaInfoDiv.innerHTML = `<span class="text-success"><i class="fas fa-check-circle"></i> Sisa kuota: ${data.sisa_kuota} dari ${data.max_kuota} orang</span>`;
                submitBtn.disabled = false;
            }
        }

        if (!data.can_book_new_trip && !data.is_trip_already_booked) {
            tripSlotInfoDiv.innerHTML = `<span class="text-danger"><i class="fas fa-times-circle"></i> Tanggal ini sudah ada ${data.jumlah_trip_berbeda} trip berbeda! Pilih tanggal lain.</span>`;
            submitBtn.disabled = true;
        } else if (!data.is_trip_already_booked) {
            tripSlotInfoDiv.innerHTML = `<span class="text-success"><i class="fas fa-check-circle"></i> Slot tersedia (${data.jumlah_trip_berbeda}/2 trip per hari)</span>`;
        } else {
            tripSlotInfoDiv.innerHTML = '';
        }
    } catch (e) {
        quotaInfoDiv.innerHTML = '<span class="text-warning">Gagal cek kuota, silakan refresh</span>';
    }
}

tripSelect.addEventListener('change',       () => { updateTotal(); checkQuota(); });
jumlahPeserta.addEventListener('input',     () => { updateTotal(); checkQuota(); });
tanggalBerangkat.addEventListener('change', checkQuota);

document.getElementById('bookingForm').addEventListener('submit', function(e) {
    const ktpFile   = document.querySelector('input[name="ktp"]').files[0];
    const buktiFile = document.querySelector('input[name="bukti"]').files[0];
    const maxSize   = 2 * 1024 * 1024;
    if (!ktpFile)               { e.preventDefault(); alert('Upload KTP/KK wajib diisi!'); return; }
    if (!buktiFile)             { e.preventDefault(); alert('Upload bukti transfer wajib diisi!'); return; }
    if (ktpFile.size > maxSize) { e.preventDefault(); alert('File KTP/KK maksimal 2MB!'); return; }
    if (buktiFile.size > maxSize){ e.preventDefault(); alert('File bukti transfer maksimal 2MB!'); return; }
    if (isSubmitting)           { e.preventDefault(); return; }
    isSubmitting = true;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
});

// Isi trip dari URL param
const urlParams  = new URLSearchParams(window.location.search);
const tripIdParam = urlParams.get('trip');
if (tripIdParam) {
    for (let i = 0; i < tripSelect.options.length; i++) {
        if (tripSelect.options[i].value == tripIdParam) {
            tripSelect.selectedIndex = i;
            updateTotal();
            break;
        }
    }
}
updateTotal();

// ======================== MODAL SIMPAN (ADMIN) ========================
@auth
@if(Auth::user()->isAdmin())
document.getElementById('btnSimpanInfo').addEventListener('click', async function() {
    const btn      = this;
    const origHTML = btn.innerHTML;
    btn.disabled   = true;
    btn.innerHTML  = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';

    const form     = document.getElementById('editInfoForm');
    const formData = new FormData(form);

    try {
        const response = await fetch('{{ route("admin.update-info-transaksi") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            // Update tampilan rekening — teks biasa, tanpa tombol salin
            document.getElementById('dispBankName').innerText   = formData.get('bank_name')   || '';
            document.getElementById('dispBankNumber').innerText = formData.get('bank_number') || '';
            document.getElementById('dispBankOwner').innerText  = formData.get('bank_owner')  || '';

            // Update teks lain
            document.getElementById('paymentInfoDisplay').innerHTML = (formData.get('payment_info')     || '').replace(/\n/g, '<br>');
            document.getElementById('termsDisplay').innerHTML       = (formData.get('terms_conditions') || '').replace(/\n/g, '<br>');
            document.getElementById('contactTextDisplay').innerText = formData.get('contact_text') || '';
            document.getElementById('whatsappLink').href            = 'https://wa.me/' + (formData.get('whatsapp_number') || '');

            // Tutup modal pakai Bootstrap API
            bootstrap.Modal.getInstance(document.getElementById('editInfoModal')).hide();
            alert('✅ Informasi berhasil disimpan!');
        } else {
            alert('❌ Gagal menyimpan: ' + (result.message || 'Terjadi kesalahan'));
        }
    } catch (err) {
        alert('❌ Error: ' + err.message);
    } finally {
        btn.disabled  = false;
        btn.innerHTML = origHTML;
    }
});
@endif
@endauth
</script>

<style>
    .whatsapp-button {
        background-color: #25D366;
        color: white;
        padding: 10px 15px;
        border-radius: 50px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all .3s ease;
        font-weight: 500;
        border: none;
    }
    .whatsapp-button:hover {
        background-color: #128C7E;
        color: white;
        transform: translateY(-2px);
    }
    #submitBtn:disabled { opacity: .6; cursor: not-allowed; }
</style>
@endsection
