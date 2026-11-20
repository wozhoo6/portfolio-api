<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\VisitorHasher;
use Illuminate\Http\Request;

use App\Jobs\RecordProjectView;
use App\Models\Project;
use Illuminate\Support\Facades\Cache;

class ProjectViewController extends Controller
{
    public function store(Request $request, string $slug, VisitorHasher $hasher)
    {
        $project = Project::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $hash = $hasher->make($request);

        $counted = Cache::add(
            "view:{$project->id}:{$hash}",
            1,
            now()->addMinutes(config('portfolio.view_dedupe_minutes'))
        );

        if ($counted) {
            RecordProjectView::dispatch($project->id, $hash);
        }

        return response()->json(['counted' => $counted], 202);
    }
}
