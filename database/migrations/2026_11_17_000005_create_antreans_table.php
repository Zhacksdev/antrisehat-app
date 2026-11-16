<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('antreans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poli_id')->constrained('polis')->restrictOnDelete();
            $table->foreignId('jadwal_dokter_id')->nullable()->constrained('jadwal_dokters')->nullOnDelete();
            $table->foreignId('pasien_id')->nullable()->constrained('pasiens')->nullOnDelete();
            $table->date('tanggal');
            $table->unsignedInteger('nomor');
            $table->string('status', 20)->default('MENUNGGU');
            $table->timestamp('dipanggil_at')->nullable();
            $table->timestamps();

            // Aturan bisnis #1: nomor unik per poli per hari.
            $table->unique(['poli_id', 'tanggal', 'nomor']);
            $table->index(['tanggal', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('antreans');
    }
};
