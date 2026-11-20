<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\ProjectView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class RecordProjectView implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $projectId, public string $visitorHash)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function (){
            ProjectView::create([
                'project_id' => $this->projectId,
                'visitor_hash' => $this->visitorHash,
                'viewed_at' => now()
            ]);

            Project::whereKey($this->projectId)->increment('view_count');
        });
    }
}
