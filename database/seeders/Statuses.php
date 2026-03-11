<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class Statuses extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status::create(
            [
                'name' => 'Backlog',
            ]
        );
        Status::create(
            [
                'name' => 'To do',
            ]
        );
        Status::create(
            [
                'name' => 'In Progress',
            ]
        );
        Status::create(
            [
                'name' => 'In Review',
            ]
        );
        Status::create(
            [
                'name' => 'Done',
            ]
        );
        Status::create(
            [
                'name' => 'Canceled',
            ]
        );
    }
}
