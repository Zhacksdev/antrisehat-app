<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('janji_temus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokter_id')->constrained('dokters')->cascadeOnDelete();
            $table->foreignId('jadwal_dokter_id')->constrained('jadwal_dokters')->cascadeOnDelete();
            $table->foreignId('pasien_id')->constrained('pasiens')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('status', 20)->default('DIJADWALKAN');
            $table->timestamps();

            $table->index(['dokter_id', 'tanggal']);
            $table->index(['jadwal_dokter_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('janji_temus');
    }
};
