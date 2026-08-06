<?php

namespace App\Http\Requests;

use App\Enums\DownloadPermission;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StoreResearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'abstract' => ['required', 'string', 'min:20'],
            'category_id' => ['nullable', 'exists:research_categories,id'],
            'keywords' => ['nullable', 'string', 'max:500'],
            'doi' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^10\.\d{4,9}\/[-._;()\/:A-Za-z0-9]+$/',
                Rule::unique('researches', 'doi'),
            ],
            'publication_date' => ['nullable', 'date'],
            'copyright_information' => ['nullable', 'string', 'max:1000'],
            'download_permission' => ['required', Rule::enum(DownloadPermission::class)],

            // Strict PDF validation: real MIME type + extension check + 100MB max
            // File::types() filters by MIME type, ->extensions() checks extension
            'pdf_file' => [
                'required',
                File::types(['application/pdf', 'application/x-pdf'])
                    ->extensions(['pdf'])
                    ->max(262144),
            ],

            // Strict image validation: extension + MIME type + 5MB maximum
            'thumbnail_file' => [
                'nullable',
                File::image()
                    ->extensions(['jpg', 'jpeg', 'png', 'webp'])
                    ->max(5120),
            ],

            'submit_action' => ['nullable', 'string', 'in:draft,submit'],
        ];
    }

    /**
     * Custom validation messages for upload security.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'pdf_file.required' => 'Please select a research paper PDF file to upload.',
            'pdf_file.types' => 'The manuscript must be a genuine PDF document (application/pdf MIME type required).',
            'pdf_file.extensions' => 'The manuscript file must have a .pdf extension.',
            'pdf_file.max' => 'The research paper PDF must not exceed 256MB. Consider compressing your document.',
            'thumbnail_file.image' => 'The cover must be a valid image file (JPG, PNG, or WEBP).',
            'thumbnail_file.extensions' => 'The cover image must have a .jpg, .jpeg, .png, or .webp extension.',
            'thumbnail_file.max' => 'The thumbnail image must not exceed 5MB.',
            'doi.regex' => 'The DOI format is invalid. Example: 10.1000/xyz123',
        ];
    }
}
