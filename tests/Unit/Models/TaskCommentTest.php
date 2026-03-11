<?php

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('task comment can be created', function () {
    $comment = TaskComment::factory()->create();

    expect($comment)->toBeInstanceOf(TaskComment::class);
    expect($comment->comment)->not->toBeEmpty();
});

test('task comment belongs to task', function () {
    $task = Task::factory()->create();
    $comment = TaskComment::factory()->create(['task_id' => $task->id]);

    expect($comment->task->id)->toBe($task->id);
});

test('task comment belongs to user', function () {
    $user = User::factory()->create();
    $comment = TaskComment::factory()->create(['user_id' => $user->id]);

    expect($comment->user->id)->toBe($user->id);
});
