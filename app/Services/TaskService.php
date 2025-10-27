<?php

namespace App\Services;

use App\Models\Task;
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
        $this->validateTaskData($data);

        return Task::create([
            'project_id' => $data['project_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'assigned_to' => $data['assigned_to'] ?? null,
            'due_date' => $data['due_date'] ?? null,
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
        $this->validateTaskData($data, $task->id);

        $task->update([
            'title' => $data['title'] ?? $task->title,
            'description' => $data['description'] ?? $task->description,
            'status' => $data['status'] ?? $task->status,
            'assigned_to' => $data['assigned_to'] ?? $task->assigned_to,
            'due_date' => $data['due_date'] ?? $task->due_date,
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
     * @throws ValidationException
     */
    protected function validateTaskData(array $data, ?int $taskId = null): void
    {
        $rules = [
            'project_id' => 'sometimes|required|exists:projects,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:pending,in-progress,done',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ];

        if ($taskId) {
            $rules['project_id'] = 'sometimes|exists:projects,id';
            $rules['title'] = 'sometimes|string|max:255';
        }

        $validator = validator($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}