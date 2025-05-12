<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\ProjectRoles;

class ProjectResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code_name' => $this->code_name,
            'additional' => $this->additional,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'participants' => $this->whenLoaded('users', function() {
                return $this->users->map(function($user) {
                    return [
                        'user' => new UserResource($user),
                        'role' => new RoleResource(
                            ProjectRoles::find($user->pivot->role_id)
                        )
                    ];
                });
            }),
        ];
    }
}
