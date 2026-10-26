<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CaseStudyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->session()->get('is_admin');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->input('slug') ?: $this->input('title', '')),
            'is_demo' => $this->boolean('is_demo'),
            'is_featured' => $this->boolean('is_featured'),
            'is_published' => $this->boolean('is_published'),
            // Drop empty metric rows before validating.
            'metrics' => collect($this->input('metrics', []))
                ->filter(fn ($m) => filled($m['label'] ?? null) || filled($m['after'] ?? null))
                ->values()
                ->all(),
        ]);
    }

    public function rules(): array
    {
        $id = $this->route('case_study')?->id;
        $image = ['image', 'mimes:jpg,jpeg,png,webp', 'max:8192'];

        return [
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['required', 'string', 'max:120', Rule::unique('case_studies', 'slug')->ignore($id)],
            'brand' => ['nullable', 'string', 'max:120'],
            'industry' => ['required', 'string', 'max:120'],
            'country' => ['required', 'string', 'max:120'],
            'category' => ['required', Rule::in(array_keys(config('portfolio.categories')))],
            'platforms' => ['required', 'array', 'min:1'],
            'platforms.*' => [Rule::in(array_keys(config('portfolio.platform_labels')))],
            'period' => ['nullable', 'string', 'max:80'],
            'strategy' => ['required', 'string', 'max:3000'],
            'improvements' => ['nullable', 'string', 'max:3000'],
            'metrics' => ['array', 'max:8'],
            'metrics.*.label' => ['required', 'string', 'max:60'],
            'metrics.*.before' => ['nullable', 'string', 'max:30'],
            'metrics.*.after' => ['required', 'string', 'max:30'],
            'metrics.*.unit' => ['nullable', 'string', 'max:10'],
            'metrics.*.direction' => ['nullable', Rule::in(['up', 'down'])],
            'is_demo' => ['boolean'],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'before_images' => ['array', 'max:10'],
            'before_images.*' => $image,
            'after_images' => ['array', 'max:10'],
            'after_images.*' => $image,
            'images' => ['array'],
            'images.*.caption' => ['nullable', 'string', 'max:200'],
            'images.*.sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'images.*.delete' => ['nullable', 'boolean'],
            'new_captions.before' => ['nullable', 'string', 'max:200'],
            'new_captions.after' => ['nullable', 'string', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return [
            'metrics.*.label.required' => 'Each metric needs a label (e.g. ROAS, Cost per lead).',
            'metrics.*.after.required' => 'Each metric needs an "after" value.',
            'before_images.*.max' => 'Screenshots must be 8 MB or smaller.',
            'after_images.*.max' => 'Screenshots must be 8 MB or smaller.',
            'platforms.required' => 'Pick at least one advertising platform.',
        ];
    }
}
