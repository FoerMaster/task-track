<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectRoles extends Model
{
    public $timestamps = false;

    public function projectUsers()
    {
        return $this->hasMany(ProjectUsers::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_users', 'role_id', 'project_id');
    }
}
