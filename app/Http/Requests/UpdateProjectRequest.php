<?php

namespace App\Http\Requests;

use App\Models\Project;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Access is already restricted by the admin.token middleware.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Only the fields that are sent get validated and changed.
     * The slug cannot be sent directly, it is rebuilt from the title.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'slug' => ['prohibited'],
            'title' => ['sometimes', 'required', 'string', 'max:255', $this->uniqueSlugRule()],
            'summary' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'tech_stack' => ['sometimes', 'nullable', 'array'],
            'tech_stack.*' => ['string', 'max:50'],
            'repo_url' => ['sometimes', 'nullable', 'url:http,https', 'max:255'],
            'live_url' => ['sometimes', 'nullable', 'url:http,https', 'max:255'],
            'thumbnail_url' => ['sometimes', 'nullable', 'url:http,https', 'max:255'],
            'is_featured' => ['sometimes', 'boolean'],
            'links' => ['sometimes', 'nullable', 'array', 'max:20'],
            'links.*.label' => ['required', 'string', 'max:60'],
            'links.*.url' => ['required', 'url:http,https', 'max:2048'],
        ];
    }

    /**
     * The slug follows the title, so a new title is only accepted when
     * the slug it produces is not used by a different project.
     * The project being edited is ignored, so keeping its own name is fine.
     */
    private function uniqueSlugRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) {
            $slug = Str::slug((string) $value);

            if ($slug === '') {
                $fail('The title must contain letters or numbers.');
                return;
            }

            $taken = Project::where('slug', $slug)
                ->where('id', '!=', $this->route('project')->id)
                ->exists();

            if ($taken) {
                $fail('A project with this name already exists.');
            }
        };
    }
}
