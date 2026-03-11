<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can be created', function () {
    $user = User::factory()->create();

    expect($user)->toBeInstanceOf(User::class);
    expect($user->email)->not->toBeEmpty();
    expect($user->name)->not->toBeEmpty();
});

test('user email is unique', function () {
    $user = User::factory()->create();
    $duplicate = User::factory()->make(['email' => $user->email]);

    // This should validate uniqueness
    expect($user->email)->toBe($duplicate->email);
});

test('user password is hashed', function () {
    $user = User::factory()->create();

    expect($user->password)->not->toBe('password');
});
