<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\SendDailyNotifications;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Register custom commands
Artisan::command('notifications:daily', function () {
    $this->call('SendDailyNotifications');
})->purpose('Send daily notifications for birthdays and anniversaries');
