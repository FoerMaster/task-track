<?php

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('project can be created', function () {
    $project = Project::factory()->create();

    expect($project)->toBeInstanceOf(Project::class);
    expect($project->name)->not->toBeEmpty();
    expect($project->code_name)->not->toBeEmpty();
});

test('project has many tasks', function () {
    $project = Project::factory()->create();

    expect($project->tasks())->not->toBeNull();
});
