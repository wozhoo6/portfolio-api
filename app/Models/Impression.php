<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Impression extends Model
{
    protected $fillable = ['project_id', 'name', 'is_employer', 'message', 'visitor_hash', 'is_visible'];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'is_employer' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

}
