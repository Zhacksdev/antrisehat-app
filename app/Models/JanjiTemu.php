<?php

namespace App\Models;

use App\Enums\StatusJanjiTemu;
use App\Support\Jam;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JanjiTemu extends Model
{
    use HasFactory;

    protected $table = 'janji_temus';

    protected $fillable = ['dokter_id', 'jadwal_dokter_id', 'pasien_id', 'tanggal', 'jam_mulai', 'jam_selesai', 'status'];

    protected function casts(): array
    {
        return [
            'status' => StatusJanjiTemu::class,
        ];
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    public function jadwalDokter(): BelongsTo
    {
        return $this->belongsTo(JadwalDokter::class);
    }

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    protected function jamMulai(): Attribute
    {
        return Attribute::make(set: fn ($value) => Jam::normalisasi($value));
    }

    protected function jamSelesai(): Attribute
    {
        return Attribute::make(set: fn ($value) => Jam::normalisasi($value));
    }
}
