<?php

namespace App\Actions\Fortify;

use App\DTOs\UserProfileDTO;
use App\Models\User;
use App\Services\UserProfileService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    public function __construct(
        protected UserProfileService $profileService
    ) {}

    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     *
     * @throws ValidationException
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'institution' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],

            // ORCID iD: four groups of digits separated by hyphens (last char can be X)
            'orcid_id' => [
                'nullable',
                'string',
                'regex:/^\d{4}-\d{4}-\d{4}-\d{3}[\dX]$/',
            ],

            // Google Scholar profile URL — must point to scholar.google.com
            'google_scholar_url' => [
                'nullable',
                'url',
                'max:500',
                'regex:/^https:\/\/scholar\.google\.[a-z.]+\/citations/i',
            ],

            // Personal academic website — HTTPS required
            'website_url' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https:\/\//i',
            ],

            // Research interests as comma-separated keywords
            'research_interests' => ['nullable', 'string', 'max:500'],

            // Avatar: image only, strict MIME, 5MB max
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ], [
            'orcid_id.regex' => 'ORCID iD must be in format 0000-0002-1825-0097 (four groups of digits separated by hyphens).',
            'google_scholar_url.regex' => 'Google Scholar URL must start with https://scholar.google.com/citations',
            'website_url.regex' => 'Personal website URL must start with https://',
        ])->validateWithBag('updateProfileInformation');

        $dto = UserProfileDTO::fromArray($input);

        $this->profileService->updateProfile($user, $dto);
    }
}
