<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\ClearExpiredGuestCarts;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command(ClearExpiredGuestCarts::class)
    ->daily()
    ->withoutOverlapping()
    ->runInBackground()
    ->timezone('Asia/Tehran');
