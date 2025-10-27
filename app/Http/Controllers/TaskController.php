<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'sometimes|in:pending,in-progress,done',
                'assigned_to' => 'nullable|exists:users,id',
                'due_date' => 'nullable|date',
            ]);

            $task = $this->taskService->createTask($validatedData);

            return redirect()->back()->with('success', 'Task created successfully.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    public function update(Request $request, Task $task)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'status' => 'sometimes|in:pending,in-progress,done',
                'assigned_to' => 'nullable|exists:users,id',
                'due_date' => 'nullable|date',
            ]);

            $task = $this->taskService->updateTask($task, $validatedData);

            return redirect()->back()->with('success', 'Task updated successfully.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    public function updateStatus(Request $request, Task $task)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,in-progress,done',
            ]);

            $task = $this->taskService->updateTaskStatus($task, $validated['status']);

            return response()->json(['message' => 'Task status updated successfully.', 'task' => $task]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred.', 'error' => $e->getMessage()], 500);
        }
    }
}