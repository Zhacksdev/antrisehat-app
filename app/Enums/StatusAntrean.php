<?php

namespace App\Enums;

enum StatusAntrean: string
{
    case Menunggu = 'MENUNGGU';
    case Dipanggil = 'DIPANGGIL';
    case Selesai = 'SELESAI';
    case Kedaluwarsa = 'KEDALUWARSA';
}
