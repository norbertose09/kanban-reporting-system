<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'title' => fake()->sentence(5),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['pending', 'in-progress', 'done']),
            'assigned_to' => User::factory(),
            'due_date' => fake()->dateTimeBetween('now', '+1 month'),
        ];
    }

    public function pending(): Factory
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function inProgress(): Factory
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'in-progress',
        ]);
    }

    public function done(): Factory
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'done',
        ]);
    }
}