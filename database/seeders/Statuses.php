<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class Statuses extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status::create(
            [
                'name' => "Backlog"
            ]
        );
        Status::create(
            [
                'name' => "To do"
            ]
        );
        Status::create(
            [
                'name' => "In Progress"
            ]
        );
        Status::create(
            [
                'name' => "In Review"
            ]
        );
        Status::create(
            [
                'name' => "Done"
            ]
        );
    }
}
