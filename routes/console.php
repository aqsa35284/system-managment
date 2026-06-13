<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 🛑 AUTOMATED GUARD (CRON JOB) 🛑
// Yeh har raat theek 12 baje expired companies ko automatically kick/suspend karega
Schedule::command('tenant:lock-expired')->dailyAt('00:00');