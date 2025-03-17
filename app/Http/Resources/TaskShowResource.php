<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'project_id' => $this->project_id,
            'status' => $this->status,
            'task_type' => $this->task_type,
            'create_from' => $this->create_from,
            'updated_from' => $this->updated_from,
            'deadline' => $this->deadline,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'responsibles' => $this->responsibles->pluck('id')->toArray(),
            'executors' => $this->executors->pluck('id')->toArray(),
            'comments' => $this->comments
        ];
    }
}
