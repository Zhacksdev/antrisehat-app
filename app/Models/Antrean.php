<?php

namespace App\Models;

use App\Enums\StatusAntrean;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Antrean extends Model
{
    use HasFactory;

    protected $table = 'antreans';

    protected $fillable = ['poli_id', 'jadwal_dokter_id', 'pasien_id', 'tanggal', 'nomor', 'status', 'dipanggil_at'];

    protected function casts(): array
    {
        return [
            'nomor' => 'integer',
            'status' => StatusAntrean::class,
            'dipanggil_at' => 'datetime',
        ];
    }

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }

    public function jadwalDokter(): BelongsTo
    {
        return $this->belongsTo(JadwalDokter::class);
    }

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    /** Contoh: "U-003" untuk poli Umum nomor 3. */
    public function getKodeAttribute(): string
    {
        return sprintf('%s-%03d', $this->poli?->kode ?? 'A', $this->nomor);
    }
}
