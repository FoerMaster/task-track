<?php

use App\Models\Project;
use App\Models\ProjectRoles;
use App\Models\ProjectUsers;
use App\Models\Status;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can create task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $status = Status::factory()->create();
    $taskType = TaskType::factory()->create();
    $role = ProjectRoles::factory()->create();

    ProjectUsers::create([
        'user_id' => $user->id,
        'project_id' => $project->id,
        'role_id' => $role->id,
    ]);

    $response = $this->actingAs($user)->post('/tasks', [
        'name' => 'Task Name',
        'description' => 'Task Description',
        'project_id' => $project->id,
        'status' => $status->id,
        'task_type' => $taskType->id,
        'deadline' => now()->addDays(5)->format('Y-m-d'),
    ]);

    expect(Task::count())->toBe(1);
    expect(Task::first()->name)->toBe('Task Name');
    $response->assertRedirect();
});

test('task creation requires name', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $status = Status::factory()->create();
    $taskType = TaskType::factory()->create();
    $role = ProjectRoles::factory()->create();

    ProjectUsers::create([
        'user_id' => $user->id,
        'project_id' => $project->id,
        'role_id' => $role->id,
    ]);

    $response = $this->actingAs($user)->post('/tasks', [
        'name' => '',
        'description' => 'Task Description',
        'project_id' => $project->id,
        'status' => $status->id,
        'task_type' => $taskType->id,
        'deadline' => now()->addDays(5)->format('Y-m-d'),
    ]);

    $response->assertSessionHasErrors(['name']);
});

test('task creation requires description', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $status = Status::factory()->create();
    $taskType = TaskType::factory()->create();
    $role = ProjectRoles::factory()->create();

    ProjectUsers::create([
        'user_id' => $user->id,
        'project_id' => $project->id,
        'role_id' => $role->id,
    ]);

    $response = $this->actingAs($user)->post('/tasks', [
        'name' => 'Task Name',
        'description' => '',
        'project_id' => $project->id,
        'status' => $status->id,
        'task_type' => $taskType->id,
        'deadline' => now()->addDays(5)->format('Y-m-d'),
    ]);

    $response->assertSessionHasErrors(['description']);
});

test('task creation requires valid project', function () {
    $user = User::factory()->create();
    $status = Status::factory()->create();
    $taskType = TaskType::factory()->create();

    $response = $this->actingAs($user)->post('/tasks', [
        'name' => 'Task Name',
        'description' => 'Task Description',
        'project_id' => 999,
        'status' => $status->id,
        'task_type' => $taskType->id,
        'deadline' => now()->addDays(5)->format('Y-m-d'),
    ]);

    $response->assertSessionHasErrors(['project_id']);
});

test('task creator becomes task author', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $status = Status::factory()->create();
    $taskType = TaskType::factory()->create();
    $role = ProjectRoles::factory()->create();

    ProjectUsers::create([
        'user_id' => $user->id,
        'project_id' => $project->id,
        'role_id' => $role->id,
    ]);

    $this->actingAs($user)->post('/tasks', [
        'name' => 'Task Name',
        'description' => 'Task Description',
        'project_id' => $project->id,
        'status' => $status->id,
        'task_type' => $taskType->id,
        'deadline' => now()->addDays(5)->format('Y-m-d'),
    ]);

    expect(Task::first()->create_from)->toBe($user->id);
});

test('task can have responsible users', function () {
    $user = User::factory()->create();
    $responsible = User::factory()->create();
    $project = Project::factory()->create();
    $status = Status::factory()->create();
    $taskType = TaskType::factory()->create();
    $role = ProjectRoles::factory()->create();

    ProjectUsers::create([
        'user_id' => $user->id,
        'project_id' => $project->id,
        'role_id' => $role->id,
    ]);

    $this->actingAs($user)->post('/tasks', [
        'name' => 'Task Name',
        'description' => 'Task Description',
        'project_id' => $project->id,
        'status' => $status->id,
        'task_type' => $taskType->id,
        'deadline' => now()->addDays(5)->format('Y-m-d'),
        'responsibles' => [$responsible->id],
    ]);

    expect(Task::first()->responsibles->count())->toBe(1);
});

test('task deadline must be equal or after today', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $status = Status::factory()->create();
    $taskType = TaskType::factory()->create();
    $role = ProjectRoles::factory()->create();

    ProjectUsers::create([
        'user_id' => $user->id,
        'project_id' => $project->id,
        'role_id' => $role->id,
    ]);

    $response = $this->actingAs($user)->post('/tasks', [
        'name' => 'Task Name',
        'description' => 'Task Description',
        'project_id' => $project->id,
        'status' => $status->id,
        'task_type' => $taskType->id,
        'deadline' => now()->subDays(1)->format('Y-m-d'),
    ]);

    $response->assertSessionHasErrors(['deadline']);
});
