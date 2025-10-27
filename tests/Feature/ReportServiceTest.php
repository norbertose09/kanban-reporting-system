<?php

namespace Tests\Unit\Services;

use App\Models\Project;
use App\Models\Report;
use App\Models\Task;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ReportService $reportService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reportService = new ReportService();
    }

    /** @test */
    public function it_generates_a_report_for_a_project()
    {
        Log::shouldReceive('info')->once();

        $project = Project::factory()->create();

        Task::factory()->count(3)->create([
            'project_id' => $project->id,
            'status' => 'pending',
        ]);
        Task::factory()->count(2)->create([
            'project_id' => $project->id,
            'status' => 'done',
        ]);
        Task::factory()->count(1)->create([
            'project_id' => $project->id,
            'status' => 'in-progress',
        ]);

        $report = $this->reportService->generateProjectReport($project);

        $this->assertInstanceOf(Report::class, $report);
        $this->assertEquals(6, $report->total_tasks);
        $this->assertEquals(2, $report->completed_tasks);
        $this->assertEquals(3, $report->pending_tasks);
        $this->assertEquals(1, $report->in_progress_tasks);
        $this->assertNotNull($report->last_generated_at);
    }

    /** @test */
    public function it_updates_existing_report_instead_of_creating_new()
    {
        $project = Project::factory()->create();
        Report::factory()->create([
            'project_id' => $project->id,
            'total_tasks' => 5,
            'completed_tasks' => 2,
            'pending_tasks' => 3,
            'in_progress_tasks' => 0,
            'last_generated_at' => Carbon::now()->subDay(),
        ]);

        Task::factory()->count(2)->create([
            'project_id' => $project->id,
            'status' => 'done',
        ]);

        $this->reportService->generateProjectReport($project);

        $this->assertDatabaseCount('reports', 1);
        $this->assertDatabaseHas('reports', [
            'project_id' => $project->id,
            'completed_tasks' => 2,
        ]);
    }

    /** @test */
    public function it_returns_the_latest_project_report()
    {
        $project = Project::factory()->create();

        $oldReport = Report::factory()->create([
            'project_id' => $project->id,
            'last_generated_at' => Carbon::now()->subDays(2),
        ]);
        $newReport = Report::factory()->create([
            'project_id' => $project->id,
            'last_generated_at' => Carbon::now(),
        ]);

        $latest = $this->reportService->getLatestProjectReport($project);

        $this->assertEquals($newReport->id, $latest->id);
    }
}
