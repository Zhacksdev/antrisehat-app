<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dokter extends Model
{
    use HasFactory;

    protected $table = 'dokters';

    protected $fillable = ['nama', 'poli_id'];

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(JadwalDokter::class);
    }

    public function janjiTemus(): HasMany
    {
        return $this->hasMany(JanjiTemu::class);
    }
}
