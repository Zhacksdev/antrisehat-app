<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasien extends Model
{
    use HasFactory;

    protected $table = 'pasiens';

    protected $fillable = ['nama', 'no_hp'];

    public function antreans(): HasMany
    {
        return $this->hasMany(Antrean::class);
    }

    public function janjiTemus(): HasMany
    {
        return $this->hasMany(JanjiTemu::class);
    }
}
