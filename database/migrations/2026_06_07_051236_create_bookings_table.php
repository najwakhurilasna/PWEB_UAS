<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('trip_id')->constrained()->onDelete('cascade');
            $table->string('nama_lengkap');
            $table->string('nomor_telepon');
            $table->date('tanggal_berangkat');
            $table->integer('jumlah_peserta');
            $table->integer('total_harga');
            $table->string('ktp_path')->nullable();
            $table->string('bukti_path')->nullable();
            $table->enum('status', ['pending', 'dikonfirmasi', 'selesai', 'batal'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
