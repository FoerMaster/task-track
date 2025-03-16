<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\ProjectUsers;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'code_name' => [
                'required',
                'max:20',
                'uppercase',
                'regex:/^[A-Z]+$/u',
                'unique:projects,code_name',
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

        return back()->with('message', 'Проект успешно создан');
    }

    public function show(Project $project)
    {
        return Inertia::render('Project', [
            'project' => new ProjectResource(
                $project->load('users')
            ),
            'tasks' => Task::where('project_id',$project->id)->get()
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $userRole = ProjectUsers::where('project_id', $project->id)
            ->where('user_id', $request->user()->id)
            ->value('role_id');

        if (!in_array($userRole, [1, 2])) {
            return back()->with('error', 'Недостаточно прав для обновления проекта');
        }

        $request->validate([
            'name' => 'required|max:255',
            'code_name' => [
                'required',
                'max:20',
                'uppercase',
                'regex:/^[A-Z]+$/u',
                Rule::unique('projects', 'code_name')->ignore($project->id),
            ],
        ]);

        try {
            $project->update($request->only(['name', 'code_name']));
            return back()->with('message', 'Проект успешно обновлен');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при обновлении проекта: ' . $e->getMessage());
        }
    }

    public function participantadd(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $project = Project::findOrFail($request->project_id);

        $currentUserRole = ProjectUsers::where('project_id', $project->id)
            ->where('user_id', $request->user()->id)
            ->value('role_id');

        if (!in_array($currentUserRole, [1, 2])) {
            return back()->with('error', 'Недостаточно прав для добавления участника');
        }

        if (ProjectUsers::where('project_id', $project->id)
            ->where('user_id', $request->user_id)
            ->exists()) {
            return back()->with('error', 'Пользователь уже в проекте');
        }

        ProjectUsers::create([
            'user_id' => $request->user_id,
            'project_id' => $project->id,
            'role_id' => 3
        ]);

        return back()->with('message', 'Участник добавлен');
    }

    public function participantdestroy(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $project = Project::findOrFail($request->project_id);
        $currentUser = $request->user();

        $currentUserRole = ProjectUsers::where('project_id', $project->id)
            ->where('user_id', $currentUser->id)
            ->value('role_id');

        $targetUserRole = ProjectUsers::where('project_id', $project->id)
            ->where('user_id', $request->user_id)
            ->value('role_id');

        if ($currentUserRole >= $targetUserRole) {
            return back()->with('error', 'Недостаточно прав для удаления');
        }

        ProjectUsers::where('project_id', $project->id)
            ->where('user_id', $request->user_id)
            ->delete();

        return back()->with('message', 'Участник удален');
    }

    public function participantupdate(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|in:1,2,3',
        ]);

        $project = Project::findOrFail($request->project_id);

        $currentUserRole = ProjectUsers::where('project_id', $project->id)
            ->where('user_id', $request->user()->id)
            ->value('role_id');

        if (!in_array($currentUserRole, [1, 2])) {
            return back()->with('error', 'Недостаточно прав для изменения роли');
        }

        $targetUser = ProjectUsers::where('project_id', $project->id)
            ->where('user_id', $request->user_id)
            ->firstOrFail();

        if ($currentUserRole >= $targetUser->role_id || $request->role_id <= $currentUserRole) {
            return back()->with('error', 'Недопустимое изменение роли');
        }

        $targetUser->update(['role_id' => $request->role_id]);

        return back()->with('message', 'Роль обновлена');
    }
}
