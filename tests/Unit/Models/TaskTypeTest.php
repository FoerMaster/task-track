<?php

use App\Models\Task;
use App\Models\TaskType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('task type can be created', function () {
    $taskType = TaskType::factory()->create();

    expect($taskType)->toBeInstanceOf(TaskType::class);
    expect($taskType->name)->not->toBeEmpty();
});

test('task type has many tasks', function () {
    $taskType = TaskType::factory()->create();

    expect($taskType->tasks())->not->toBeNull();
});

test('task type can have multiple tasks', function () {
    $taskType = TaskType::factory()->create();
    Task::factory()->create(['task_type' => $taskType->id]);
    Task::factory()->create(['task_type' => $taskType->id]);

    expect($taskType->tasks->count())->toBe(2);
});
