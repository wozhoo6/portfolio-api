<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectDailyStat extends Model
{
    protected $fillable = ['project_id', 'stat_date', 'views', 'unique_visitors'];

    protected function casts(): array
    {
        return ['stat_date' => 'date'];
    }
}
