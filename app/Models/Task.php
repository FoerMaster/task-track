<?php

namespace App\Models;

use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'name',
        'description',
        'project_id',
        'status',
        'task_type',
        'create_from',
        'deadline',
        'updated_from',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status');
    }

    public function taskType()
    {
        return $this->belongsTo(TaskType::class, 'task_type');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'create_from');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function responsibles()
    {
        return $this->belongsToMany(User::class, 'responsible_user', 'task_id', 'user_id');
    }

    public function executors()
    {
        return $this->belongsToMany(User::class, 'executor_user', 'task_id', 'user_id');
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttach::class);
    }
}
