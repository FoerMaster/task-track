<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'name' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'project_id' => Project::factory(),
            'status' => Status::factory(),
            'task_type' => TaskType::factory(),
            'create_from' => User::factory(),
            'deadline' => fake()->dateTimeBetween('now', '+3 months'),
        ];
    }
}
