<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:send-watering-reminders')->dailyAt('10:00');
Schedule::command('app:check-sla')->dailyAt('02:00');
Schedule::command('app:audit-tree-data')->weeklyOn(0, '03:00');
