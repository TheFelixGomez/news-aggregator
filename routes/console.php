<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// TODO: uncomment and set up scheduling for news article fetching
//Schedule::command('news:fetch-articles')->hourly(); // Runs the command every hour
