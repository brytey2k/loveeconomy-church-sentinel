<?php

declare(strict_types=1);

use App\Jobs\FetchFxRatesJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule the FX rates fetching job every 8 hours
Schedule::job(new FetchFxRatesJob())->cron('0 */8 * * *');
