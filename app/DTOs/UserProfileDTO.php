<?php

namespace App\DTOs;

use Illuminate\Http\UploadedFile;

readonly class UserProfileDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $institution = null,
        public ?string $department = null,
        public ?string $bio = null,
        public ?string $researchInterests = null,
        public ?string $orcidId = null,
        public ?string $googleScholarUrl = null,
        public ?string $websiteUrl = null,
        public ?UploadedFile $avatar = null,
    ) {}

    /**
     * Create DTO from request array/data.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) ($data['name'] ?? ''),
            email: (string) ($data['email'] ?? ''),
            institution: isset($data['institution']) ? (string) $data['institution'] : null,
            department: isset($data['department']) ? (string) $data['department'] : null,
            bio: isset($data['bio']) ? (string) $data['bio'] : null,
            researchInterests: isset($data['research_interests']) ? (string) $data['research_interests'] : null,
            orcidId: isset($data['orcid_id']) ? (string) $data['orcid_id'] : null,
            googleScholarUrl: isset($data['google_scholar_url']) ? (string) $data['google_scholar_url'] : null,
            websiteUrl: isset($data['website_url']) ? (string) $data['website_url'] : null,
            avatar: isset($data['avatar']) && $data['avatar'] instanceof UploadedFile ? $data['avatar'] : null,
        );
    }
}
