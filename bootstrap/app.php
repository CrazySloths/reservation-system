<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth' => \App\Http\Middleware\CheckAuth::class,
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withSchedule(function ($schedule) {
        // Expire unpaid bookings every hour
        $schedule->command('bookings:expire-unpaid')->hourly();
        
        // Mark finished bookings as completed every hour
        $schedule->command('bookings:complete-finished')->hourly();
        
        // Send payment reminders (24h and 6h before deadline)
        $schedule->command('payments:send-reminders')->hourly();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

