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

}
