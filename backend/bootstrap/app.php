<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\RateLimitApi::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('sports:sync --live-only')
            ->everyMinute()
            ->withoutOverlapping()
            ->sendOutputTo('/dev/stdout');

        $schedule->command('sports:sync')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->sendOutputTo('/dev/stdout');

        $schedule->call(function () {
            app(\App\Services\FootballDataService::class)->cleanupOldMatches(30);
        })->dailyAt('03:00');
    })
    ->create();