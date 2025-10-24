<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{
    protected $guarded = [];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'task_type');
    }
}
