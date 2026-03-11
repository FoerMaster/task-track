<?php

namespace Database\Factories;

use App\Models\TaskType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskType>
 */
class TaskTypeFactory extends Factory
{
    protected $model = TaskType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Bug', 'Feature', 'Enhancement', 'Documentation', 'Task']),
        ];
    }
}
