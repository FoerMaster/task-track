<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProjectRoles;

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
