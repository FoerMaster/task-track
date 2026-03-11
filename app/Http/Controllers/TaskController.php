<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskShowResource;
use App\Models\Status;
use App\Models\Task;
use App\Models\TaskAttach;
use App\Models\TaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'responsibles' => 'nullable|array',
            'responsibles.*' => 'exists:users,id',
            'executors' => 'nullable|array',
            'executors.*' => 'exists:users,id',
            'files' => 'nullable|array',
            'files.*' => [
                'file',
                'max:4096',
                function ($attribute, $value, $fail) {
                    $forbiddenExtensions = ['php', 'phar', 'html', 'htm', 'js', 'exe', 'bat', 'sh', 'phtml', 'asp', 'aspx', 'jsp', 'jar'];
                    $extension = strtolower($value->getClientOriginalExtension());

                    if (in_array($extension, $forbiddenExtensions, true)) {
                        $fail("Файлы с расширением .$extension запрещены!");
                    }
                },
            ],
        ], [
            'name.required' => 'Название задачи обязательно для заполнения.',
            'name.string' => 'Название задачи должно быть строкой.',
            'name.max' => 'Название задачи не должно превышать 255 символов.',
            'description.required' => 'Описание задачи обязательно для заполнения.',
            'description.string' => 'Описание задачи должно быть строкой.',
            'project_id.required' => 'Проект обязателен для выбора.',
            'project_id.exists' => 'Выбранный проект недопустим.',
            'status.required' => 'Статус задачи обязателен для выбора.',
            'status.exists' => 'Выбранный статус недопустим.',
            'task_type.required' => 'Тип задачи обязателен для выбора.',
            'task_type.exists' => 'Выбранный тип задачи недопустим.',
            'deadline.required' => 'Срок выполнения задачи обязателен для заполнения.',
            'deadline.date' => 'Срок выполнения задачи должен быть датой.',
            'deadline.after_or_equal' => 'Срок выполнения задачи должен быть после или равным текущей дате.',
            'responsibles.array' => 'Ответственные должны быть переданы в виде массива.',
            'responsibles.*.exists' => 'Выбранный ответственный недопустим.',
            'executors.array' => 'Исполнители должны быть переданы в виде массива.',
            'executors.*.exists' => 'Выбранный исполнитель недопустим.',
            'files.array' => 'Файлы должны быть переданы в виде массива',
            'files.*.file' => 'Каждый элемент должен быть файлом',
            'files.*.max' => 'Размер файла не должен превышать 4 МБ',
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

        if ($request->has('responsibles')) {
            $task->responsibles()->attach($request->responsibles);
        }

        if ($request->has('executors')) {
            $task->executors()->attach($request->executors);
        }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $fileName = uniqid() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('uploads', $fileName, 'public');

                TaskAttach::create([
                    'task_id' => $task->id,
                    'file_name' => $file->getClientOriginalName(),
                    'attachment_url' => 'storage/' . $path,
                ]);
            }
        }

        return redirect()->route('tasks.show', $task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $task->load('responsibles:id', 'executors:id', 'comments', 'attachments');
        return Inertia::render('Task',
            [
                'task' => new TaskShowResource($task),
            ]
        );
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
        ], [
            'name.required' => 'Название задачи обязательно для заполнения.',
            'name.string' => 'Название задачи должно быть строкой.',
            'name.max' => 'Название задачи не должно превышать 255 символов.',
            'description.required' => 'Описание задачи обязательно для заполнения.',
            'description.string' => 'Описание задачи должно быть строкой.',
            'project_id.required' => 'Проект обязателен для выбора.',
            'project_id.exists' => 'Выбранный проект недопустим.',
            'status.required' => 'Статус задачи обязателен для выбора.',
            'status.exists' => 'Выбранный статус недопустим.',
            'task_type.required' => 'Тип задачи обязателен для выбора.',
            'task_type.exists' => 'Выбранный тип задачи недопустим.',
            'deadline.required' => 'Срок выполнения задачи обязателен для заполнения.',
            'deadline.date' => 'Срок выполнения задачи должен быть датой.',
            'deadline.after_or_equal' => 'Срок выполнения задачи должен быть после или равным текущей дате.',
            'responsibles.array' => 'Ответственные должны быть переданы в виде массива.',
            'responsibles.*.exists' => 'Выбранный ответственный недопустим.',
            'executors.array' => 'Исполнители должны быть переданы в виде массива.',
            'executors.*.exists' => 'Выбранный исполнитель недопустим.',
        ]);

        $toUpdate = $request->only($task->getFillable());
        $toUpdate['updated_from'] = $request->user()->id;

        if ($request->status && $request->status != $task->status) {
            $fromStatus = Status::find($task->status);
            $toStatus = Status::find($request->status);
            TaskComment::create([
                "task_id" => $task->id,
                "user_id" => $request->user()->id,
                "comment" => '![ACTION] Изменил статус с <span class="text-rose-500">'.$fromStatus->name.'</span> на <span class="text-rose-500">'.$toStatus->name.'</span>',
            ]);
        }

        $task->update($toUpdate);

        if ($request->has('responsibles')) {
            if (empty($request->responsibles)) {
                // Если передан пустой массив, очищаем связи
                $task->responsibles()->detach();
            } else {
                // Если передан непустой массив, синхронизируем связи
                $task->responsibles()->sync($request->responsibles);
            }
        }

        // Обновляем executors
        if ($request->has('executors')) {
            if (empty($request->executors)) {
                // Если передан пустой массив, очищаем связи
                $task->executors()->detach();
            } else {
                // Если передан непустой массив, синхронизируем связи
                $task->executors()->sync($request->executors);
            }
        }




        return back()->with('message','Вы успешно обновили информацию по данной задаче');
    }

    public function attachmentadd(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'files' => 'required|array',
            'files.*' => [
                'file',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $forbiddenExtensions = ['php', 'phar', 'html', 'htm', 'js', 'exe', 'bat', 'sh', 'phtml', 'asp', 'aspx', 'jsp', 'jar'];
                    $extension = strtolower($value->getClientOriginalExtension());

                    if (in_array($extension, $forbiddenExtensions, true)) {
                        $fail("Файлы с расширением .$extension запрещены!");
                    }
                },
            ],
        ]);

        foreach ($request->file('files') as $file) {
            $fileName = uniqid() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads', $fileName, 'public');

            TaskAttach::create([
                'task_id' => $request->task_id,
                'file_name' => $file->getClientOriginalName(),
                'attachment_url' => 'storage/' . $path,
            ]);
        }

        return back()->with('message', 'Файлы добавлены');
    }

    public function attachmentdestroy(Request $request)
    {
        $request->validate([
            'attachment_id' => 'required|exists:task_attaches,id',
        ]);

        $attach = TaskAttach::find($request->attachment_id);

        if (!$attach) {
            return back()->with('error', 'Вложение не найдено');
        }

        if ($attach->attachment_url) {
            Storage::disk('public')->delete(str_replace('storage/', '', $attach->attachment_url));
        }

        $attach->delete();

        return back()->with('message', 'Вложение удалено');
    }
}
