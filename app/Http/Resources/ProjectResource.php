<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'summary' => $this->summary,
            'description' => $this->description,
            'role' => $this->role,
            'problem' => $this->problem,
            'solution' => $this->solution,
            'key_features' => $this->key_features ?? [],
            'tech_stack' => $this->tech_stack ?? [],
            'repo_url' => $this->repo_url,
            'live_url' => $this->live_url,
            'thumbnail_url' => $this->thumbnail_url,
            'is_featured' => $this->is_featured,
            'view_count' => $this->view_count,
            'reactions_count' => $this->reactions_count,
            'links' => $this->whenLoaded(
                'links',
                fn () => $this->links->map(fn ($link) => [
                    'label' => $link->label,
                    'url' => $link->url,
                ])->values()
            ),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
