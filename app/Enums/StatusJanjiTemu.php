<?php

namespace App\Enums;

enum StatusJanjiTemu: string
{
    case Dijadwalkan = 'DIJADWALKAN';
    case Selesai = 'SELESAI';
    case Batal = 'BATAL';

    /** Status yang masih menempati slot dan kuota sesi. */
    public static function aktif(): array
    {
        return [self::Dijadwalkan->value, self::Selesai->value];
    }
}
