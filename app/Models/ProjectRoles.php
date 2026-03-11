<?php

namespace App\Models;

use Database\Factories\ProjectRolesFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectRoles extends Model
{
    /** @use HasFactory<ProjectRolesFactory> */
    use HasFactory;
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
