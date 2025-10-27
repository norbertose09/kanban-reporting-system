<?php

namespace Tests\Unit; // or Tests\Feature if you prefer

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase; // To reset database for each test
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase; // This trait migrates the database for each test and rolls it back

    protected TaskService $taskService;
    protected User $adminUser;
    protected User $memberUser;
    protected Project $project;

    // This method runs before each test method
    protected function setUp(): void
    {
        parent::setUp();

        // Instantiate the service
        $this->taskService = new TaskService();

        // Create some test data using factories
        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->memberUser = User::factory()->create(['role' => 'member']);
        $this->project = Project::factory()->create();
    }

    /** @test */
    public function it_can_create_a_task_with_valid_data(): void
    {
        $data = [
            'project_id' => $this->project->id,
            'title' => 'New Task Title',
            'description' => 'This is a test task description.',
            'status' => 'pending',
            'assigned_to' => $this->memberUser->id,
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ];

        $task = $this->taskService->createTask($data);

        // Assertions
        $this->assertInstanceOf(Task::class, $task); // Check if it returned a Task instance
        $this->assertDatabaseHas('tasks', [ // Check if the task exists in the database
            'project_id' => $data['project_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => $data['status'],
            'assigned_to' => $data['assigned_to'],
            'due_date' => $data['due_date'] . ' 00:00:00',
        ]);
        $this->assertEquals($data['title'], $task->title); // Check specific properties
        $this->assertEquals($data['status'], $task->status);
    }

    /** @test */
    public function it_can_create_a_task_with_minimal_valid_data(): void
    {
        $data = [
            'project_id' => $this->project->id,
            'title' => 'Minimal Task',
        ];

        $task = $this->taskService->createTask($data);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertDatabaseHas('tasks', [
            'project_id' => $data['project_id'],
            'title' => $data['title'],
            'status' => 'pending', // Default status
            'assigned_to' => null, // Default assigned_to
        ]);
        $this->assertEquals('pending', $task->status);
        $this->assertNull($task->description);
    }

    /** @test */
    public function it_throws_validation_exception_when_creating_task_with_invalid_project_id(): void
    {
        $this->expectException(ValidationException::class);

        $data = [
            'project_id' => 9999, // Non-existent project ID
            'title' => 'Invalid Project Task',
        ];

        $this->taskService->createTask($data);
    }

    /** @test */
    public function it_throws_validation_exception_when_creating_task_without_title(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The title field is required.'); // Optionally assert the error message

        $data = [
            'project_id' => $this->project->id,
            // 'title' is missing
        ];

        $this->taskService->createTask($data);
    }

    /** @test */
    public function it_can_update_an_existing_task_with_valid_data(): void
    {
        $task = Task::factory()->for($this->project)->create([
            'assigned_to' => $this->memberUser->id,
            'status' => 'pending'
        ]);

        $updateData = [
            'title' => 'Updated Task Title',
            'status' => 'in-progress',
            'assigned_to' => $this->adminUser->id,
        ];

        $updatedTask = $this->taskService->updateTask($task, $updateData);

        $this->assertInstanceOf(Task::class, $updatedTask);
        $this->assertEquals($updateData['title'], $updatedTask->title);
        $this->assertEquals($updateData['status'], $updatedTask->status);
        $this->assertEquals($updateData['assigned_to'], $updatedTask->assigned_to);

        $this->assertDatabaseHas('tasks', [ // Verify in database
            'id' => $task->id,
            'title' => $updateData['title'],
            'status' => $updateData['status'],
            'assigned_to' => $updateData['assigned_to'],
        ]);
    }

    /** @test */
    public function it_throws_validation_exception_when_updating_task_with_invalid_assigned_to(): void
    {
        $this->expectException(ValidationException::class);

        $task = Task::factory()->for($this->project)->create();

        $updateData = [
            'assigned_to' => 9999, // Non-existent user
        ];

        $this->taskService->updateTask($task, $updateData);
    }

    /** @test */
    public function it_can_update_task_status(): void
    {
        $task = Task::factory()->for($this->project)->create(['status' => 'pending']);

        $updatedTask = $this->taskService->updateTaskStatus($task, 'in-progress');

        $this->assertInstanceOf(Task::class, $updatedTask);
        $this->assertEquals('in-progress', $updatedTask->status);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'in-progress',
        ]);
    }

    /** @test */
    public function it_throws_validation_exception_for_invalid_status_update(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid task status provided.');

        $task = Task::factory()->for($this->project)->create(['status' => 'pending']);

        $this->taskService->updateTaskStatus($task, 'invalid-status');
    }
}