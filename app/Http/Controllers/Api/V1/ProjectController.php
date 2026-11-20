<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Http\Resources\ProjectResource;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('links')
            ->where('is_published', true)
            ->orderByDesc('is_featured')
            ->latest()
            ->get();

        return ProjectResource::collection($projects);
    }

    public function show(string $slug){
        $project = Project::with('links')
        ->where('slug', $slug)
        ->where('is_published', true)
        ->firstOrFail();

        return new ProjectResource($project);
    }


}
