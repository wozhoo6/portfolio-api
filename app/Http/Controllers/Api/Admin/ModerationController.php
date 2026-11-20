<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImpressionResource;
use App\Models\Impression;

class ModerationController extends Controller
{
    public function index()
    {
        return ImpressionResource::collection(
            Impression::with('project:id,slug')->latest()->paginate(20)
        );
    }

    public function hide(Impression $impression)
    {
        $impression->update(['is_visible' => false]);

        return new ImpressionResource($impression->load('project:id,slug'));
    }

    public function show(Impression $impression)
    {
        $impression->update(['is_visible' => true]);

        return new ImpressionResource($impression->load('project:id,slug'));
    }

    public function destroy(Impression $impression)
    {
        $impression->delete();

        return response()->noContent();
    }
}
