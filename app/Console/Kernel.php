<?php

namespace App\Console;

use App\Console\Commands\ImportCapturesCommand;
use App\Console\Commands\ImportOperatorsCommand;
use App\Console\Commands\MakeStoragePublicCommand;
use App\Console\Commands\SendOperatorsCredentialsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ImportOperatorsCommand::class,
        ImportCapturesCommand::class,
        SendOperatorsCredentialsCommand::class,
        MakeStoragePublicCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('queue:work --daemon')->everyMinute()->withoutOverlapping();
    }
}
