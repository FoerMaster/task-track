<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\ProjectUsers;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Projects');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'code_name' => [
                'required',
                'max:20',
                'unique:projects,code_name',
                'regex:/^[a-zA-Z]+$/u',
                'uppercase'
            ],
        ]);

        $project = Project::create([
            'name' => $request->name,
            'code_name' => $request->code_name,
        ]);

        ProjectUsers::create([
            'user_id' => $request->user()->id,
            'project_id' => $project->id,
            'role_id' => 1
        ]);

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {

        return Inertia::render('Project', [
            'project' => new ProjectResource(
                $project->load('users')
            )
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
    }
}
