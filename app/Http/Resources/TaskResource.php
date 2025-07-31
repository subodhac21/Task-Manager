<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TaskResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            "image_path" =>  $this->image ? asset('storage/' . $this->image) : asset('images/default-task.png'),
            'status' => new StatusResource($this->whenLoaded('status')),
            'assignee' => new UserResource($this->whenLoaded('assignee')),
            'priority' => $this->priority,
            'assigned_at' => $this->assigned_at,
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
        ];
    }
}
