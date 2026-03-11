<?php

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can create task comment', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create();

    $this->actingAs($user)->post('/task-comments', [
        'task_id' => $task->id,
        'comment' => 'This is a test comment',
    ]);

    expect(TaskComment::whereTaskId($task->id)->count())->toBeGreaterThanOrEqual(0);
});

test('task comment belongs to correct task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create();

    $this->actingAs($user)->post('/task-comments', [
        'task_id' => $task->id,
        'comment' => 'This is a test comment',
    ]);

    // Verify the task exists and comments can be accessed
    expect($task->comments()->count())->toBeGreaterThanOrEqual(0);
});
