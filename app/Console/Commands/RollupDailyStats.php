<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\ProjectDailyStat;
use App\Models\ProjectView;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RollupDailyStats extends Command
{
    protected $signature = 'stats:rollup {date? : YYYY-MM-DD, defaults to yesterday}';

    protected $description = 'Roll up project views into daily stats and re-sync reaction counters';

    public function handle(): int
    {
        $date = $this->argument('date') ?? now()->subDay()->toDateString();

        $rows = ProjectView::query()
            ->selectRaw('project_id, count(*) as views, count(distinct visitor_hash) as unique_visitors')
            ->whereDate('viewed_at', $date)
            ->groupBy('project_id')
            ->get();

        foreach ($rows as $row) {
            ProjectDailyStat::updateOrCreate(
                ['project_id' => $row->project_id, 'stat_date' => $date],
                ['views' => $row->views, 'unique_visitors' => $row->unique_visitors]
            );
        }

        // Counter sync: make projects.reactions_count match the reactions table
        Project::query()->update([
            'reactions_count' => DB::raw('(select count(*) from reactions where reactions.project_id = projects.id)'),
        ]);

        $this->info("Rolled up {$rows->count()} project(s) for {$date}.");

        return self::SUCCESS;
    }
}
