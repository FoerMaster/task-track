<?php

use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('task can be created', function () {
    $task = Task::factory()->create();

    expect($task)->toBeInstanceOf(Task::class);
    expect($task->name)->not->toBeEmpty();
    expect($task->description)->not->toBeEmpty();
});

test('task belongs to project', function () {
    $project = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id]);

    expect($task->project->id)->toBe($project->id);
});

test('task belongs to status', function () {
    $status = Status::factory()->create();
    $task = Task::factory()->create(['status' => $status->id]);

    expect($task->status()->first()->id)->toBe($status->id);
});

test('task belongs to task type', function () {
    $taskType = TaskType::factory()->create();
    $task = Task::factory()->create(['task_type' => $taskType->id]);

    expect($task->taskType()->first()->id)->toBe($taskType->id);
});

test('task belongs to creator', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['create_from' => $user->id]);

    expect($task->creator->id)->toBe($user->id);
});

test('task has many comments', function () {
    $task = Task::factory()->create();

    expect($task->comments())->not->toBeNull();
});

test('task can have multiple responsible users', function () {
    $task = Task::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $task->responsibles()->attach([$user1->id, $user2->id]);

    expect($task->responsibles->count())->toBe(2);
});
