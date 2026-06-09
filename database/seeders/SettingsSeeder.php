<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Informasi Pemesanan
            [
                'key' => 'payment_info',
                'value' => 'DP 50% dari total harga<br>Sisa pembayaran dapat diinformasikan via WhatsApp<br>Admin akan menghubungi Anda setelah booking dikonfirmasi',
                'type' => 'textarea'
            ],
            [
                'key' => 'terms_conditions',
                'value' => 'Booking dianggap sah setelah upload bukti transfer<br>Pembatalan H-7: refund 50%<br>Pembatalan H-3: refund 25%<br>Pembatalan H-1: tidak ada refund',
                'type' => 'textarea'
            ],
            [
                'key' => 'quota_info',
                'value' => 'Setiap paket trip maksimal 8 orang per tanggal keberangkatan<br>Silakan pilih tanggal yang masih tersedia kuotanya',
                'type' => 'textarea'
            ],
            [
                'key' => 'whatsapp_number',
                'value' => '6282340188130',
                'type' => 'text'
            ],
            [
                'key' => 'contact_text',
                'value' => 'Butuh bantuan? Hubungi Admin',
                'type' => 'text'
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']]
            );
        }
    }
}
