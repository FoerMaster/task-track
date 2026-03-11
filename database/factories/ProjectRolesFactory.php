<?php

namespace Database\Factories;

use App\Models\ProjectRoles;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectRoles>
 */
class ProjectRolesFactory extends Factory
{
    protected $model = ProjectRoles::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Owner', 'Manager', 'Member', 'Viewer']),
        ];
    }
}
