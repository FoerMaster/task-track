<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAttach extends Model
{
    protected $fillable = ['task_id', 'file_name', 'attachment_url'];
}
