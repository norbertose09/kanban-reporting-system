<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateReportJob;
use App\Models\Project;
use App\Services\ReportService;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index()
    {
        $projects = Project::with('latestReport')->get();

        return Inertia::render('ReportsPage', [
            'projects' => $projects->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'latest_report' => $project->latestReport ? [
                        'total_tasks' => $project->latestReport->total_tasks,
                        'completed_tasks' => $project->latestReport->completed_tasks,
                        'pending_tasks' => $project->latestReport->pending_tasks,
                        'in_progress_tasks' => $project->latestReport->in_progress_tasks,
                        'last_generated_at' => $project->latestReport->last_generated_at->diffForHumans(),
                    ] : null,
                ];
            }),
        ]);
    }

    public function generate(Request $request)
    {
        GenerateReportJob::dispatch();

        return redirect()->back()->with('success', 'Report generation job dispatched. Reports will be updated shortly.');
    }
}