@extends('layouts.app')

@section('title', 'Form Pemesanan - NajaTrip')

@section('content')
<div class="row">
    {{-- FORM PEMESANAN --}}
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
                            <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', Auth::user()->name) }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="tel" name="nomor_telepon" class="form-control" placeholder="08xxxxxxxxxx" value="{{ old('nomor_telepon') }}" required>
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
                            <input type="date" name="tanggal_berangkat" id="tanggalBerangkat" class="form-control" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                            <div id="quotaInfo" class="small mt-1"></div>
                            <div id="tripSlotInfo" class="small mt-1"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jumlah Peserta <span class="text-danger">*</span> (Max 8 orang)</label>
                            <input type="number" name="jumlah_peserta" id="jumlahPeserta" class="form-control" min="1" max="8" value="1" required>
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
                            <label class="form-label">Catatan (opsional)</label>
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Contoh: request penjemputan, diet khusus, dll"></textarea>
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

    {{-- INFO PANEL KANAN --}}
    <div class="col-md-4">
        {{-- TOMBOL EDIT UNTUK ADMIN --}}
        @auth
            @if(Auth::user()->isAdmin())
                <div class="mb-3">
                    <button type="button" class="btn btn-warning w-100" onclick="openEditModal()">
                        <i class="fas fa-edit"></i> Edit Informasi Transaksi
                    </button>
                </div>
            @endif
        @endauth

        {{-- Info Pembayaran --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Pemesanan</h5>
            </div>
            <div class="card-body">
                <p><strong>💳 Cara Pembayaran:</strong></p>
                <div class="small" id="paymentInfoDisplay">
                    {!! nl2br(e($paymentInfo ?? 'DP 50% dari total harga')) !!}
                </div>
                <hr>
                <p><strong>📋 Syarat & Ketentuan:</strong></p>
                <div class="small" id="termsDisplay">
                    {!! nl2br(e($termsConditions ?? 'Booking dianggap sah setelah upload bukti transfer')) !!}
                </div>
                <hr>
                <p><strong>📞 <span id="contactTextDisplay">{{ $contactText ?? 'Butuh bantuan?' }}</span></strong></p>
                <a href="https://wa.me/{{ $whatsappNumber ?? '6282340188130' }}" id="whatsappLink" class="whatsapp-button w-100 justify-content-center" target="_blank">
                    <i class="fab fa-whatsapp"></i> Chat Admin
                </a>
            </div>
        </div>

        {{-- Info Kuota --}}
        <div class="card shadow-sm">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="fas fa-users"></i> Informasi Kuota</h5>
            </div>
            <div class="card-body">
                <div class="small">
                    <p>✅ <strong>Max 8 orang</strong> per trip per tanggal</p>
                    <p>✅ <strong>Max 2 trip berbeda</strong> per tanggal</p>
                    <p>⚠️ Jika kuota penuh, booking tidak dapat diproses</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT INFORMASI --}}
<div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; width: 90%; max-width: 600px; border-radius: 16px; padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #ddd;">
            <h3><i class="fas fa-edit"></i> Edit Informasi Transaksi</h3>
            <button type="button" onclick="closeEditModal()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        <form id="editInfoForm">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold;">💳 Informasi Pembayaran</label>
                <textarea name="payment_info" id="paymentInfoInput" class="form-control" rows="4"></textarea>
                <small>Gunakan Enter untuk baris baru</small>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold;">📋 Syarat & Ketentuan</label>
                <textarea name="terms_conditions" id="termsInput" class="form-control" rows="5"></textarea>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold;">📊 Informasi Kuota</label>
                <textarea name="quota_info" id="quotaInfoInput" class="form-control" rows="3"></textarea>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold;">📞 Nomor WhatsApp</label>
                <input type="text" name="whatsapp_number" id="whatsappInput" class="form-control">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold;">💬 Teks Tombol WhatsApp</label>
                <input type="text" name="contact_text" id="contactTextInput" class="form-control">
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn btn-primary" style="flex:1;">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()" style="flex:1;">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    // DOM Elements
    const tripSelect = document.getElementById('tripSelect');
    const jumlahPeserta = document.getElementById('jumlahPeserta');
    const totalDisplay = document.getElementById('totalHargaDisplay');
    const totalInput = document.getElementById('totalHargaInput');
    const tanggalBerangkat = document.getElementById('tanggalBerangkat');
    const quotaInfoDiv = document.getElementById('quotaInfo');
    const tripSlotInfoDiv = document.getElementById('tripSlotInfo');
    const submitBtn = document.getElementById('submitBtn');

    let isSubmitting = false;

    // Update total harga
    function updateTotal() {
        const selected = tripSelect.options[tripSelect.selectedIndex];
        const harga = selected.dataset.harga || 0;
        const peserta = parseInt(jumlahPeserta.value) || 1;
        const total = harga * peserta;
        totalDisplay.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        totalInput.value = total;
    }

    // Cek kuota dari server (MAX 8 ORANG & MAX 2 TRIP BERBEDA PER TANGGAL)
    async function checkQuota() {
        const tripId = tripSelect.value;
        const tanggal = tanggalBerangkat.value;

        if (tripId && tanggal) {
            try {
                const response = await fetch(`/api/check-quota?trip_id=${tripId}&tanggal=${tanggal}`);
                const data = await response.json();

                // ========== INFO 1: KUOTA PER TRIP (MAX 8 ORANG) ==========
                if (data.is_quota_full) {
                    quotaInfoDiv.innerHTML = `<span class="text-danger"><i class="fas fa-times-circle"></i> Kuota trip ini sudah penuh! (${data.total_terpakai}/${data.max_kuota} orang)</span>`;
                    submitBtn.disabled = true;
                } else {
                    quotaInfoDiv.innerHTML = `<span class="text-success"><i class="fas fa-check-circle"></i> Sisa kuota trip ini: ${data.sisa_kuota} dari ${data.max_kuota} orang</span>`;

                    const peserta = parseInt(jumlahPeserta.value) || 1;
                    if (peserta > data.sisa_kuota) {
                        quotaInfoDiv.innerHTML += `<br><span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Jumlah peserta (${peserta}) melebihi sisa kuota (${data.sisa_kuota})!</span>`;
                        submitBtn.disabled = true;
                    } else {
                        submitBtn.disabled = false;
                    }
                }

                // ========== INFO 2: TRIP BERBEDA PER TANGGAL (MAX 2 TRIP) ==========
                if (data.is_trip_already_booked) {
                    // Trip yang sudah ada di tanggal tersebut → BOLEH (tambah peserta)
                    tripSlotInfoDiv.innerHTML = `<span class="text-success"><i class="fas fa-check-circle"></i> Anda sudah memesan trip ini di tanggal tersebut. Silakan lanjutkan booking.</span>`;
                    // Jangan nonaktifkan tombol (kecuali kuota penuh)
                    if (!data.is_quota_full && quotaInfoDiv.innerHTML.indexOf('melebihi') === -1) {
                        submitBtn.disabled = false;
                    }
                } else if (data.can_book_new_trip) {
                    // Trip berbeda dan masih ada slot (belum 2 trip berbeda)
                    tripSlotInfoDiv.innerHTML = `<span class="text-success"><i class="fas fa-check-circle"></i> Masih tersedia slot untuk trip baru (${data.jumlah_trip_berbeda}/2 trip berbeda per hari)</span>`;
                    if (!data.is_quota_full && quotaInfoDiv.innerHTML.indexOf('melebihi') === -1) {
                        submitBtn.disabled = false;
                    }
                } else {
                    // Trip berbeda tapi sudah mencapai 2 trip berbeda → TIDAK BOLEH
                    tripSlotInfoDiv.innerHTML = `<span class="text-danger"><i class="fas fa-times-circle"></i> Tanggal ini sudah memiliki ${data.jumlah_trip_berbeda} trip berbeda! Maksimal 2 trip berbeda per hari. Silakan pilih tanggal lain.</span>`;
                    submitBtn.disabled = true;
                }
            } catch (error) {
                console.error('Error checking quota:', error);
                quotaInfoDiv.innerHTML = '<span class="text-warning">Gagal cek kuota, silakan refresh halaman</span>';
            }
        } else {
            quotaInfoDiv.innerHTML = '';
            tripSlotInfoDiv.innerHTML = '';
            submitBtn.disabled = false;
        }
    }

    // Validasi sebelum submit
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        const ktpFile = document.querySelector('input[name="ktp"]').files[0];
        const buktiFile = document.querySelector('input[name="bukti"]').files[0];

        if (!ktpFile) {
            e.preventDefault();
            alert('Upload KTP/KK wajib diisi!');
            return false;
        }

        if (!buktiFile) {
            e.preventDefault();
            alert('Upload bukti transfer wajib diisi!');
            return false;
        }

        const maxSize = 2 * 1024 * 1024;
        if (ktpFile.size > maxSize) {
            e.preventDefault();
            alert('File KTP/KK maksimal 2MB!');
            return false;
        }

        if (buktiFile.size > maxSize) {
            e.preventDefault();
            alert('File bukti transfer maksimal 2MB!');
            return false;
        }

        if (isSubmitting) {
            e.preventDefault();
            return false;
        }

        isSubmitting = true;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    });

    // Event listeners
    tripSelect.addEventListener('change', () => {
        updateTotal();
        checkQuota();
    });

    jumlahPeserta.addEventListener('input', () => {
        updateTotal();
        checkQuota();
    });

    tanggalBerangkat.addEventListener('change', checkQuota);

    // Initial call
    updateTotal();

    // Jika ada trip_id dari parameter URL
    const urlParams = new URLSearchParams(window.location.search);
    const tripIdParam = urlParams.get('trip');
    if (tripIdParam) {
        for(let i = 0; i < tripSelect.options.length; i++) {
            if(tripSelect.options[i].value == tripIdParam) {
                tripSelect.selectedIndex = i;
                updateTotal();
                break;
            }
        }
    }

    // Open modal dan isi data
    function openEditModal() {
        document.getElementById('paymentInfoInput').value = document.getElementById('paymentInfoDisplay').innerText;
        document.getElementById('termsInput').value = document.getElementById('termsDisplay').innerText;
        document.getElementById('quotaInfoInput').value = document.getElementById('quotaInfoDisplay').innerText;
        document.getElementById('whatsappInput').value = document.getElementById('whatsappLink').href.replace('https://wa.me/', '');
        document.getElementById('contactTextInput').value = document.getElementById('contactTextDisplay').innerText;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Submit edit info
    document.getElementById('editInfoForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

        const formData = new FormData(this);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        try {
            const response = await fetch('{{ route("admin.update-info-transaksi") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                document.getElementById('paymentInfoDisplay').innerHTML = result.data.payment_info.replace(/\n/g, '<br>');
                document.getElementById('termsDisplay').innerHTML = result.data.terms_conditions.replace(/\n/g, '<br>');
                document.getElementById('quotaInfoDisplay').innerHTML = result.data.quota_info.replace(/\n/g, '<br>');
                document.getElementById('contactTextDisplay').innerText = result.data.contact_text;
                document.getElementById('whatsappLink').href = 'https://wa.me/' + result.data.whatsapp_number;
                closeEditModal();
                alert('Informasi berhasil diupdate!');
            } else {
                alert('Gagal menyimpan: ' + result.message);
            }
        } catch (error) {
            alert('Error: ' + error.message);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // Tutup modal jika klik di luar
    window.onclick = function(event) {
        const modal = document.getElementById('editModal');
        if (event.target === modal) {
            closeEditModal();
        }
    }
</script>

<style>
    .whatsapp-button {
        background-color: #25D366;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 50px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
    }
    .whatsapp-button:hover {
        background-color: #128C7E;
        color: white;
        transform: translateY(-2px);
    }
    button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>
@endsection
