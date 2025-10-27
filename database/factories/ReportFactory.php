<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'total_tasks' => $this->faker->numberBetween(0, 10),
            'completed_tasks' => $this->faker->numberBetween(0, 10),
            'pending_tasks' => $this->faker->numberBetween(0, 10),
            'in_progress_tasks' => $this->faker->numberBetween(0, 10),
            'last_generated_at' => Carbon::now(),
        ];
    }
}
