<?php

namespace Database\Seeders;

use App\Models\TaskType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskTypes extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TaskType::create([
            'name' => "Feature request"
        ]);
        TaskType::create([
            'name' => "Bug"
        ]);
    }
}
