<?php

namespace App\Jobs;

use App\Models\Project;
use App\Services\ReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ReportService $reportService): void
    {
        Log::info('GenerateReportJob started.');

        $projects = Project::all();

        foreach ($projects as $project) {
            try {
                $reportService->generateProjectReport($project);
            } catch (\Exception $e) {
                Log::error("Error generating report for Project ID: {$project->id} - {$project->name}. Error: {$e->getMessage()}");
            }
        }

        Log::info('GenerateReportJob finished.');
    }
}