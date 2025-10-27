<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TaskService
{
    /**
     * Create a new task.
     *
     * @param array $data
     * @return Task
     * @throws ValidationException
     */
    public function createTask(array $data): Task
    {
        $validated = $this->validateTaskData($data);

        return Task::create([
            'project_id' => $validated['project_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? 'pending',
            'assigned_to' => $validated['assigned_to'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
        ]);
    }

    /**
     * Update an existing task.
     *
     * @param Task $task
     * @param array $data
     * @return Task
     * @throws ValidationException
     */
    public function updateTask(Task $task, array $data): Task
    {
        $validated = $this->validateTaskData($data, $task->id);

        $task->update([
            'title' => $validated['title'] ?? $task->title,
            'description' => $validated['description'] ?? $task->description,
            'status' => $validated['status'] ?? $task->status,
            'assigned_to' => $validated['assigned_to'] ?? $task->assigned_to,
            'due_date' => $validated['due_date'] ?? $task->due_date,
        ]);

        return $task;
    }

    /**
     * Update task status (for drag-and-drop).
     *
     * @param Task $task
     * @param string $newStatus
     * @return Task
     * @throws ValidationException
     */
    public function updateTaskStatus(Task $task, string $newStatus): Task
    {
        if (!in_array($newStatus, ['pending', 'in-progress', 'done'])) {
            throw ValidationException::withMessages([
                'status' => 'Invalid task status provided.',
            ]);
        }

        $task->update(['status' => $newStatus]);
        return $task;
    }

    /**
     * Internal validation helper.
     *
     * @param array $data
     * @param int|null $taskId
     * @return array
     * @throws ValidationException
     */
    protected function validateTaskData(array $data, ?int $taskId = null): array
    {
        $rules = [
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:pending,in-progress,done',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ];

        if ($taskId) {
            foreach ($rules as $key => &$rule) {
                $rule = 'sometimes|' . $rule;
            }
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
