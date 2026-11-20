<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class AdminProjectResource extends ProjectResource
{
    /**
     * Same as the public resource, plus the fields only an admin needs.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            ['id' => $this->id],
            parent::toArray($request),
            [
                'is_published' => $this->is_published,
                'updated_at' => $this->updated_at?->toIso8601String(),
            ]
        );
    }
}
