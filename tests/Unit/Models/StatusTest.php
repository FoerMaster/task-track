<?php

use App\Models\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('status can be created', function () {
    $status = Status::factory()->create();

    expect($status)->toBeInstanceOf(Status::class);
    expect($status->name)->not->toBeEmpty();
});

test('status has valid name', function () {
    $status = Status::factory()->create(['name' => 'To Do']);

    expect($status->name)->toBe('To Do');
});
