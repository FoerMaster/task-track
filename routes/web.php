<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use \App\Http\Controllers\TaskCommentController;
use App\Http\Resources\TaskShowResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect('/login');
})->name('home');

Route::get('dashboard', function () {
    $tasks = Task::with('responsibles', 'executors')->get();
    return Inertia::render('Dashboard',['tasks'=> TaskShowResource::collection($tasks)]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('agiles', function (Request $request) {
    $user = $request->user();

    // Получаем задачи, где текущий пользователь является исполнителем
    $tasks = Task::with('responsibles', 'executors')
        ->whereHas('executors', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })
        ->get();

    return Inertia::render('MyKanban', [
        'tasks' => TaskShowResource::collection($tasks)
    ]);
})->middleware(['auth', 'verified'])->name('agiles');

Route::get('create_task', function () {
    return Inertia::render('CreateTask');
})->middleware(['auth', 'verified'])->name('create_task');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('projects', ProjectController::class);
    Route::prefix('projects')->group(function () {
        Route::post('participant', [ProjectController::class, 'participantadd'])->name('projects.participant.add');
        Route::post('participant/destroy', [ProjectController::class, 'participantdestroy'])->name('projects.participant.destroy');
        Route::post('participant/update', [ProjectController::class, 'participantupdate'])->name('projects.participant.update');
    });
    Route::resource('tasks', TaskController::class);
    Route::prefix('tasks')->group(function () {
        Route::prefix('tasks')->resource('comment', TaskCommentController::class);
    });


});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
