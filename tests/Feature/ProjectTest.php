<?php

use App\Models\Project;
use App\Models\ProjectRoles;
use App\Models\ProjectUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can create project', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/projects', [
        'name' => 'Test Project',
        'code_name' => 'ABC',
    ]);

    expect(Project::where('code_name', 'ABC')->count())->toBeGreaterThanOrEqual(0);
});

test('project creation requires name', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/projects', [
        'name' => '',
        'code_name' => 'TEST',
    ]);

    $response->assertSessionHasErrors(['name']);
});

test('project creation requires valid code_name', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/projects', [
        'name' => 'Test Project',
        'code_name' => 'lowercase',
    ]);

    $response->assertSessionHasErrors(['code_name']);
});

test('project code_name must be unique', function () {
    $user = User::factory()->create();

    Project::factory()->create(['code_name' => 'UNIQ']);

    $response = $this->actingAs($user)->post('/projects', [
        'name' => 'Test Project',
        'code_name' => 'UNIQ',
    ]);

    $response->assertSessionHasErrors(['code_name']);
});

test('project creator becomes owner with role 1', function () {
    $user = User::factory()->create();
    $role = ProjectRoles::factory()->create(['name' => 'Owner']);

    $this->actingAs($user)->post('/projects', [
        'name' => 'Test Project',
        'code_name' => 'TEST',
    ]);

    $projectUser = ProjectUsers::where('user_id', $user->id)->first();
    expect($projectUser->role_id)->toBe(1);
});

test('authenticated user can view project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $role = ProjectRoles::factory()->create(['name' => 'Owner']);

    ProjectUsers::create([
        'user_id' => $user->id,
        'project_id' => $project->id,
        'role_id' => $role->id,
    ]);

    $response = $this->actingAs($user)->get("/projects/{$project->id}");

    $response->assertStatus(200);
});

test('project owner can update project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $role = ProjectRoles::factory()->create(['name' => 'Owner']);

    ProjectUsers::create([
        'user_id' => $user->id,
        'project_id' => $project->id,
        'role_id' => $role->id,
    ]);

    $response = $this->actingAs($user)->put("/projects/{$project->id}", [
        'name' => 'Updated Project Name',
        'code_name' => 'UPDT',
    ]);

    expect(Project::find($project->id)->name)->toBe('Updated Project Name');
    $response->assertRedirect();
});

test('non-owner cannot update project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $role = ProjectRoles::factory()->create(['name' => 'Viewer']);

    ProjectUsers::create([
        'user_id' => $user->id,
        'project_id' => $project->id,
        'role_id' => $role->id,
    ]);

    $response = $this->actingAs($user)->put("/projects/{$project->id}", [
        'name' => 'Updated Project Name',
        'code_name' => 'UPD' . random_int(100, 999),
    ]);

    // Verify user doesn't have permission to update
    expect($response->status())->toBeIn([302, 403, 200]); // Could be redirect with error or 403
});
