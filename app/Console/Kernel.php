<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\MqttListener::class,
        \App\Console\Commands\CheckMealTimeout::class,
        \App\Console\Commands\RunMealScheduler::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('meal:run-scheduler')
            ->everyMinute()
            ->withoutOverlapping();

        $schedule->command('check:meal-timeout')
            ->everyMinute()
            ->withoutOverlapping();
    }
}