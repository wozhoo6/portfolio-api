<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreImpressionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60'],
            'is_employer' => ['sometimes', 'boolean'],
            'message' => ['required', 'string', 'min:3', 'max:500'],
            'project_slug' => ['nullable', 'string', 'exists:projects,slug'],
            'website' => ['nullable', 'string'],
            'cf_turnstile_token' => ['nullable', 'string'],
        ];
    }
}
