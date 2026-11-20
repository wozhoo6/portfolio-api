<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Reaction;
use App\Services\VisitorHasher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReactionController extends Controller
{
    public function toggle(Request $request, string $slug, VisitorHasher $hasher)
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(config('portfolio.reaction_types'))]
        ]);

        $project = Project::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $hash = $hasher->make($request);


        $active = DB::transaction(function () use ($project, $hash, $data) {
            $reaction = Reaction::firstOrCreate([
                'project_id' => $project->id,
                'visitor_hash' => $hash,
                'type' => $data['type']
            ]);

            if ($reaction->wasRecentlyCreated) {
                $project->increment('reactions_count');
                return true;
            }

            $reaction->delete();
            $project->decrement('reactions_count');
            return false;
        });

        return response()->json([
            'active' => $active,
            'total' => $project->fresh()->reactions_count,
            'by_type' => $project->reactions()
                ->selectRaw('type, count(*) as total')
                ->groupBy('type')
                ->pluck('total', 'type')
        ]);
    }
}
