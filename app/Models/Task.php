<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = [];

    protected $fillable = [
        'name',
        'description',
        'project_id',
        'status',
        'task_type',
        'create_from',
        'deadline',
        'updated_from'
    ];
}
