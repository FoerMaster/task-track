<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'timezone',
        'full_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_users')
            ->withPivot('role_id')
            ->using(ProjectUsers::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'create_from');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function responsibleTasks()
    {
        return $this->belongsToMany(Task::class, 'responsible_user');
    }

    public function executorTasks()
    {
        return $this->belongsToMany(Task::class, 'executor_user');
    }
}
