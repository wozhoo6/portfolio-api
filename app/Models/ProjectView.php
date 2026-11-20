<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectView extends Model
{
    protected $fillable = ['project_id', 'visitor_hash', 'viewed_at'];

    public $timestamps = false;

    protected function casts(): array
    {
        return ['viewed_at' => 'datetime'];
    }
}
