<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    // name - max:255, reuired, striped
    // code_name - max:20, reuired, latinonly, striped, uniq
    // project_owner - guarded
    // owner, manger, mainter,
    protected $fillable = ['name', 'code_name', 'owner_id'];
    protected $hidden = ['pivot'];
    public function users()
    {
        return $this->belongsToMany(User::class, 'project_users')
            ->withPivot('role_id')
            ->using(ProjectUsers::class); // Если используете промежуточную модель
    }

    public function roles()
    {
        return $this->hasManyThrough(
            ProjectRoles::class,
            ProjectUsers::class,
            'project_id', // Внешний ключ в project_users
            'id',         // Локальный ключ в project_roles
            'id',         // Локальный ключ в projects
            'role_id'     // Внешний ключ в project_users
        );
    }
}

