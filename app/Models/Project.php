<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'summary',
        'description',
        'role',
        'problem',
        'solution',
        'key_features',
        'tech_stack',
        'repo_url',
        'live_url',
        'thumbnail_url',
        'is_published',
        'is_featured',
    ];

    protected $casts = [
        'tech_stack' => 'array',
        'key_features' => 'array',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }

    public function impressions(): HasMany
    {
        return $this->hasMany(Impression::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(ProjectLink::class)->orderBy('sort_order');
    }

}
