<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poli extends Model
{
    use HasFactory;

    protected $table = 'polis';

    protected $fillable = ['nama', 'kode'];

    public function dokters(): HasMany
    {
        return $this->hasMany(Dokter::class);
    }

    public function antreans(): HasMany
    {
        return $this->hasMany(Antrean::class);
    }
}
