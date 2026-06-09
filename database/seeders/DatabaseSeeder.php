<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Trip;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin NajaTrip',
            'email' => 'admin@najatrip.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Customer Demo
        User::create([
            'name' => 'Customer Demo',
            'email' => 'customer@demo.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        $trips = [
            ['nama' => 'Kawah Ijen', 'lokasi' => 'Banyuwangi', 'deskripsi' => 'Menyaksikan fenomena blue fire di Kawah Ijen. Pendakian malam yang tak terlupakan.', 'harga' => 350000, 'durasi' => 2, 'fasilitas' => json_encode(['Transportasi PP', 'Guide Lokal', 'Masker Gas', 'Dokumentasi', 'Makan']), 'status' => 'aktif'],
            ['nama' => 'Pantai Boom', 'lokasi' => 'Banyuwangi', 'deskripsi' => 'Nikmati keindahan sunset di Pantai Boom, spot foto favorit.', 'harga' => 200000, 'durasi' => 1, 'fasilitas' => json_encode(['Transportasi', 'Dokumentasi', 'Minuman']), 'status' => 'aktif'],
            ['nama' => 'Pulau Tabuhan', 'lokasi' => 'Banyuwangi', 'deskripsi' => 'Pulau kecil dengan air laut super jernih, cocok untuk snorkeling.', 'harga' => 450000, 'durasi' => 1, 'fasilitas' => json_encode(['Perahu', 'Snorkeling Gear', 'Makan Siang', 'Guide']), 'status' => 'aktif'],
            ['nama' => 'Nusa Penida', 'lokasi' => 'Bali', 'deskripsi' => 'Jelajahi keindahan pantai Kelingking, Broken Beach, dan Angel Billabong.', 'harga' => 450000, 'durasi' => 1, 'fasilitas' => json_encode(['Fast Boat PP', 'Makan Siang', 'Transportasi', 'Tour Guide']), 'status' => 'aktif'],
            ['nama' => 'Bedugul & Tanah Lot', 'lokasi' => 'Bali', 'deskripsi' => 'Wisata ke Danau Beratan dan Pura Tanah Lot ikonik.', 'harga' => 350000, 'durasi' => 1, 'fasilitas' => json_encode(['Transportasi', 'Tiket Masuk', 'Guide', 'Dokumentasi']), 'status' => 'aktif'],
            ['nama' => 'Ubud & Tegalalang', 'lokasi' => 'Bali', 'deskripsi' => 'Wisata budaya di Ubud, Monkey Forest, dan Tegalalang Rice Terrace.', 'harga' => 300000, 'durasi' => 1, 'fasilitas' => json_encode(['Transportasi', 'Tour Guide', 'Air Mineral', 'Dokumentasi']), 'status' => 'aktif'],
        ];

        foreach ($trips as $trip) {
            Trip::create($trip);
        }
    }
}
