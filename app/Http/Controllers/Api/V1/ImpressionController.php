<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImpressionRequest;
use App\Http\Resources\ImpressionResource;
use App\Models\Impression;
use App\Models\Project;
use App\Services\TurnstileVerifier;
use App\Services\VisitorHasher;
use Illuminate\Http\Request;

class ImpressionController extends Controller
{
    public function index(Request $request)
    {
        $query = Impression::with('project:id,slug')
            ->where('is_visible', true)
            ->latest();

        if ($slug = $request->query('project')) {
            $query->whereHas('project', fn($q) => $q->where('slug', $slug));
        } elseif ($request->query('scope') === 'portfolio') {
            $query->whereNull('project_id');
        }

        return ImpressionResource::collection($query->paginate(10));
    }


    public function store(StoreImpressionRequest $request, VisitorHasher $hasher, TurnstileVerifier $turnstile)
    {
        $data = $request->validated();

        if (!empty($data['website'])) {
            return response()->json(['message' => 'Thanks!'], 202);
        }

        if (!$turnstile->passes($data['cf_turnstile_token'] ?? null)) {
            return response()->json(['message' => 'Verification failed.'], 422);
        }


        $projectId = !empty($data['project_slug'])
            ? Project::where('slug', $data['project_slug'])->value('id')
            : null;

        $impression = Impression::create([
            'project_id' => $projectId,
            'name' => strip_tags($data['name']),
            'is_employer' => $data['is_employer'] ?? false,
            'message' => strip_tags($data['message']),
            'visitor_hash' => $hasher->make($request),
        ]);

        return (new ImpressionResource($impression->load('project:id,slug')))
            ->response()
            ->setStatusCode(201);

    }


}
