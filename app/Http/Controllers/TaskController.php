<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('CreateTask');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'project_id' => 'required|exists:projects,id',
            'status' => 'required|exists:statuses,id',
            'task_type' => 'required|exists:task_types,id',
            'deadline' => 'required|date|after_or_equal:today',
        ]);

        $task = Task::create([
            'name' => $request->name,
            'description' => $request->description,
            'project_id' => $request->project_id,
            'status' => $request->status,
            'task_type' => $request->task_type,
            'create_from' => $request->user()->id,
            'deadline' => $request->deadline,
        ]);

        return redirect()->route('tasks.show', $task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return Inertia::render('Task',['task' => $task]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'project_id' => 'sometimes|required|exists:projects,id',
            'status' => 'sometimes|required|exists:statuses,id',
            'task_type' => 'sometimes|required|exists:task_types,id',
            'deadline' => 'sometimes|required|date|after_or_equal:today',
        ]);

        $task->update($request->only($task->getFillable()));

        return redirect()->route('tasks.show', $task)->with('message','Вы успешно обновили информацию по данной задаче');
    }
}
