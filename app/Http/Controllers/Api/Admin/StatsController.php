<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Impression;
use App\Models\Project;
use App\Models\ProjectDailyStat;
use App\Models\Reaction;

class StatsController extends Controller
{
    public function index()
    {
        return response()->json([
            'totals' => [
                'projects' => Project::count(),
                'views' => (int) Project::sum('view_count'),
                'impressions' => Impression::count(),
                'hidden_impressions' => Impression::where('is_visible', false)->count(),
                'reactions' => Reaction::count(),
            ],
            'top_projects' => Project::orderByDesc('view_count')
                ->limit(5)
                ->get(['slug', 'title', 'view_count', 'reactions_count']),
            'daily' => ProjectDailyStat::selectRaw('stat_date, sum(views) as views, sum(unique_visitors) as unique_visitors')
                ->where('stat_date', '>=', now()->subDays(14)->toDateString())
                ->groupBy('stat_date')
                ->orderBy('stat_date')
                ->get(),
        ]);
    }
}
