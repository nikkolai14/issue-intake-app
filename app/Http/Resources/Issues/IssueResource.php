<?php

namespace App\Http\Resources\Issues;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IssueResource extends JsonResource
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
            'priority' => [
                'value' => $this->priority?->value,
                'label' => $this->priority?->label(),
            ],
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
            ],
            'summary' => $this->summary,
            'next_action' => $this->next_action,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
