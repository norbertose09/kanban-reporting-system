<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReportService
{
    /**
     * Generate and store a report for a given project.
     *
     * @param Project $project
     * @return Report
     */
    public function generateProjectReport(Project $project): Report
    {
        $totalTasks = $project->tasks()->count();
        $completedTasks = $project->tasks()->where('status', 'done')->count();
        $pendingTasks = $project->tasks()->where('status', 'pending')->count();
        $inProgressTasks = $project->tasks()->where('status', 'in-progress')->count();

        $report = Report::updateOrCreate(
            [
                'project_id' => $project->id,
            ],
            [
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'pending_tasks' => $pendingTasks,
                'in_progress_tasks' => $inProgressTasks,
                'last_generated_at' => Carbon::now(),
            ]
        );

        Log::info("Report generated for Project ID: {$project->id} - '{$project->name}' at {$report->last_generated_at}");

        return $report;
    }

    /**
     * Get the latest report for a given project.
     *
     * @param Project $project
     * @return Report|null
     */
    public function getLatestProjectReport(Project $project): ?Report
    {
        return $project->reports()->latest('last_generated_at')->first();
    }
}