<?php

namespace App\Http\Requests;

use App\Models\Project;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreProjectRequest extends FormRequest
{
    /**
     * Access is already restricted by the admin.token middleware.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', $this->uniqueSlugRule()],
            'slug' => ['prohibited'],
            'summary' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'tech_stack' => ['nullable', 'array'],
            'tech_stack.*' => ['string', 'max:50'],
            'repo_url' => ['nullable', 'url:http,https', 'max:255'],
            'live_url' => ['nullable', 'url:http,https', 'max:255'],
            'thumbnail_url' => ['nullable', 'url:http,https', 'max:255'],
            'is_published' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'links' => ['nullable', 'array', 'max:20'],
            'links.*.label' => ['required', 'string', 'max:60'],
            'links.*.url' => ['required', 'url:http,https', 'max:2048'],
        ];
    }

    /**
     * The slug is built from the title, so the title is only accepted
     * when the slug it produces is not already used by another project.
     */
    private function uniqueSlugRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) {
            $slug = Str::slug((string) $value);

            if ($slug === '') {
                $fail('The title must contain letters or numbers.');
            } elseif (Project::where('slug', $slug)->exists()) {
                $fail('A project with this name already exists.');
            }
        };
    }
}
