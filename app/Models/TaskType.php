<?php

namespace App\Models;

use Database\Factories\TaskTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{
    /** @use HasFactory<TaskTypeFactory> */
    use HasFactory;

    protected $guarded = [];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'task_type');
    }
}
