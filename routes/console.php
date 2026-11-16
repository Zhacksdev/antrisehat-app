<?php

use Illuminate\Support\Facades\Schedule;

/*
| Scheduler reset antrean harian (PRD 2.8).
| Dijalankan oleh container `scheduler` via `php artisan schedule:work`.
| Default setara dailyAt('00:00'); set ANTRISEHAT_RESET_CRON="* * * * *" untuk demo.
*/
Schedule::command('antrean:reset')
    ->cron(config('antrisehat.reset_cron'))
    ->withoutOverlapping();
