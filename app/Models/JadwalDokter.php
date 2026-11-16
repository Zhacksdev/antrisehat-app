<?php

namespace App\Models;

use App\Support\Jam;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalDokter extends Model
{
    use HasFactory;

    public const NAMA_HARI = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];

    protected $table = 'jadwal_dokters';

    protected $fillable = ['dokter_id', 'hari', 'jam_mulai', 'jam_selesai', 'maks_pasien'];

    protected function casts(): array
    {
        return [
            'hari' => 'integer',
            'maks_pasien' => 'integer',
        ];
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    public function janjiTemus(): HasMany
    {
        return $this->hasMany(JanjiTemu::class);
    }

    public function antreans(): HasMany
    {
        return $this->hasMany(Antrean::class);
    }

    public function getNamaHariAttribute(): string
    {
        return self::NAMA_HARI[$this->hari] ?? '-';
    }

    public function getLabelAttribute(): string
    {
        return sprintf(
            '%s, %s - %s',
            $this->nama_hari,
            Jam::pendek($this->jam_mulai),
            Jam::pendek($this->jam_selesai),
        );
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
