<?php

namespace App\Console;

use App\Jobs\GenerateReportJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new GenerateReportJob())->dailyAt('00:00')
                 ->onSuccess(function () {
                     Log::info('GenerateReportJob dispatched successfully by scheduler.');
                 })
                 ->onFailure(function () {
                     Log::error('GenerateReportJob failed to dispatch by scheduler.');
                 })
                 ->appendOutputTo(storage_path('logs/scheduler.log'));
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}