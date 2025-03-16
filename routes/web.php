<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Models\Task;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard',['tasks'=>Task::all()]);
})->middleware(['auth', 'verified'])->name('dashboard');



Route::get('create_task', function () {
    return Inertia::render('CreateTask');
})->middleware(['auth', 'verified'])->name('create_task');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('projects', ProjectController::class);
    Route::prefix('projects')->post('participant', [ProjectController::class, 'participantadd'])->name('projects.participant.add');
    Route::prefix('projects')->post('participant/destroy', [ProjectController::class, 'participantdestroy'])->name('projects.participant.destroy');
    Route::prefix('projects')->post('participant/update', [ProjectController::class, 'participantupdate'])->name('projects.participant.update');
    Route::resource('tasks', TaskController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
