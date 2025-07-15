<?php

namespace App\Http\Middleware;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\ProjectRoles;
use App\Models\Status;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $authProps = $request->user() ? [
            'user' => $request->user(),
            'assigned_projects' => $request->user()->projects()->get() ?? collect(),
            'roles' => ProjectRoles::all(),
            'users_list' => User::all(),
            'projects' => ProjectResource::collection(Project::all()),
            'statuses' => Status::all(),
            'task_types' => TaskType::all(),
        ] : [];

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => $authProps,
            'flash' => [
                'message' => fn () => $request->session()->get('message')
            ],
        ];
    }
}
