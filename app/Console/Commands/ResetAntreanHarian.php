<?php

namespace App\Console\Commands;

use App\Enums\StatusAntrean;
use App\Models\Antrean;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResetAntreanHarian extends Command
{
    protected $signature = 'antrean:reset
                            {--tanggal= : Tanggal acuan (Y-m-d). Default: hari ini}';

    protected $description = 'Menandai antrean hari sebelumnya yang masih MENUNGGU sebagai KEDALUWARSA dan menutup sesi yang sudah lewat';

    public function handle(): int
    {
        $acuan = $this->option('tanggal')
            ? Carbon::parse($this->option('tanggal'))->toDateString()
            : today()->toDateString();

        [$kedaluwarsa, $ditutup] = DB::transaction(function () use ($acuan) {
            $kedaluwarsa = Antrean::query()
                ->where('tanggal', '<', $acuan)
                ->where('status', StatusAntrean::Menunggu->value)
                ->update(['status' => StatusAntrean::Kedaluwarsa->value, 'updated_at' => now()]);

            $ditutup = Antrean::query()
                ->where('tanggal', '<', $acuan)
                ->where('status', StatusAntrean::Dipanggil->value)
                ->update(['status' => StatusAntrean::Selesai->value, 'updated_at' => now()]);

            return [$kedaluwarsa, $ditutup];
        });

        $ringkasan = sprintf(
            '[antrean:reset] acuan=%s kedaluwarsa=%d sesi_ditutup=%d total_diproses=%d',
            $acuan,
            $kedaluwarsa,
            $ditutup,
            $kedaluwarsa + $ditutup,
        );

        Log::info($ringkasan, compact('acuan', 'kedaluwarsa', 'ditutup'));
        $this->info($ringkasan);

        return self::SUCCESS;
    }
}
