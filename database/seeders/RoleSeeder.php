<?php

namespace Database\Seeders;

use App\Models\ProjectRoles;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProjectRoles::create(
            ['name' => 'Owner'],
        );
        ProjectRoles::create(
            ['name' => 'Manager'],
        );
        ProjectRoles::create(
            ['name' => 'User'],
        );
    }
}
