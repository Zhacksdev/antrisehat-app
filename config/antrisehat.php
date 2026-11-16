<?php

return [
    // Aturan bisnis #3: kuota default per sesi dokter.
    'maks_pasien_per_sesi' => (int) env('ANTRISEHAT_MAKS_PASIEN', 30),

    // Durasi default satu slot janji temu (menit) bila jam_selesai tidak diisi.
    'durasi_slot_menit' => (int) env('ANTRISEHAT_DURASI_SLOT', 15),

    // Jadwal cron untuk antrean:reset. Default tengah malam (dailyAt 00:00);
    // untuk demo scheduler bisa diisi "* * * * *".
    'reset_cron' => env('ANTRISEHAT_RESET_CRON', '0 0 * * *'),
];
