<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\AdminProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectAdminController extends Controller
{
    /**
     * Optional filters: ?status=active|inactive, ?featured=1|0, ?search=title text
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:active,inactive'],
            'featured' => ['nullable', 'boolean'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $projects = Project::with('links')
            ->when(($filters['status'] ?? null) === 'active', fn ($q) => $q->where('is_published', true))
            ->when(($filters['status'] ?? null) === 'inactive', fn ($q) => $q->where('is_published', false))
            ->when(isset($filters['featured']), fn ($q) => $q->where('is_featured', $request->boolean('featured')))
            ->when(! empty($filters['search']), fn ($q) => $q->where(
                'title',
                'like',
                '%'.addcslashes($filters['search'], '%_\\').'%'
            ))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return AdminProjectResource::collection($projects);
    }

    public function show(Project $project)
    {
        return new AdminProjectResource($project->load('links'));
    }

    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();
        $links = Arr::pull($data, 'links') ?? [];

        $data['slug'] = Str::slug($data['title']);

        $project = DB::transaction(function () use ($data, $links) {
            $project = Project::create($data);
            $this->syncLinks($project, $links);

            return $project;
        });

        return (new AdminProjectResource($project->load('links')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $data = $request->validated();
        $hasLinks = array_key_exists('links', $data);
        $links = Arr::pull($data, 'links') ?? [];

        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        DB::transaction(function () use ($project, $data, $hasLinks, $links) {
            $project->update($data);

            if ($hasLinks) {
                $this->syncLinks($project, $links);
            }
        });

        return new AdminProjectResource($project->load('links'));
    }

    public function activate(Project $project)
    {
        $project->update(['is_published' => true]);

        return new AdminProjectResource($project->load('links'));
    }

    public function deactivate(Project $project)
    {
        $project->update(['is_published' => false]);

        return new AdminProjectResource($project->load('links'));
    }

    /**
     * Replace the project's whole link list with the one sent.
     * The array order becomes the display order.
     */
    private function syncLinks(Project $project, array $links): void
    {
        $project->links()->delete();

        foreach (array_values($links) as $order => $link) {
            $project->links()->create([
                'label' => $link['label'],
                'url' => $link['url'],
                'sort_order' => $order,
            ]);
        }
    }
}
