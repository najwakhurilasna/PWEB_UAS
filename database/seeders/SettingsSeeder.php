<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'payment_info'     => 'DP 50% dari total harga<br>Sisa pembayaran dapat diinformasikan via WhatsApp<br>Admin akan menghubungi Anda setelah booking dikonfirmasi',
            'terms_conditions' => 'Booking dianggap sah setelah upload bukti transfer<br>Pembatalan H-7: refund 50%<br>Pembatalan H-3: refund 25%<br>Pembatalan H-1: tidak ada refund',
            'quota_info'       => 'Setiap paket trip maksimal 8 orang per tanggal keberangkatan<br>Silakan pilih tanggal yang masih tersedia kuotanya',
            'whatsapp_number'  => '6282340188130',
            'contact_text'     => 'Butuh bantuan? Hubungi Admin',
            'bank_name'        => 'BRI',
            'bank_number'      => '1234567890',
            'bank_owner'       => 'a.n. Admin NajaTrip',
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
