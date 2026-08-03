<?php

namespace App\DTOs;

use App\Enums\DownloadPermission;
use App\Enums\ResearchStatus;
use Illuminate\Http\UploadedFile;

readonly class ResearchData
{
    public function __construct(
        public string $title,
        public string $abstract,
        public ?string $keywords = null,
        public ?int $categoryId = null,
        public ?string $publicationDate = null,
        public ?string $doi = null,
        public ?string $copyrightInformation = null,
        public DownloadPermission $downloadPermission = DownloadPermission::FREE,
        public ResearchStatus $status = ResearchStatus::PUBLISHED,
        public ?UploadedFile $pdfFile = null,
        public ?UploadedFile $thumbnailFile = null,
        /** @var array<int> */
        public array $coAuthorIds = [],
    ) {}

    /**
     * Create ResearchData DTO from request input.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $status = ResearchStatus::PUBLISHED;

        if (isset($data['submit_action']) && $data['submit_action'] === 'draft') {
            $status = ResearchStatus::DRAFT;
        } elseif (isset($data['status'])) {
            $status = ResearchStatus::from((string) $data['status']);
        }

        return new self(
            title: (string) ($data['title'] ?? ''),
            abstract: (string) ($data['abstract'] ?? ''),
            keywords: isset($data['keywords']) ? (string) $data['keywords'] : null,
            categoryId: ! empty($data['category_id']) ? (int) $data['category_id'] : null,
            publicationDate: isset($data['publication_date']) ? (string) $data['publication_date'] : null,
            doi: isset($data['doi']) ? (string) $data['doi'] : null,
            copyrightInformation: isset($data['copyright_information']) ? (string) $data['copyright_information'] : null,
            downloadPermission: isset($data['download_permission'])
                ? DownloadPermission::from((string) $data['download_permission'])
                : DownloadPermission::FREE,
            status: $status,
            pdfFile: isset($data['pdf_file']) && $data['pdf_file'] instanceof UploadedFile ? $data['pdf_file'] : null,
            thumbnailFile: isset($data['thumbnail_file']) && $data['thumbnail_file'] instanceof UploadedFile ? $data['thumbnail_file'] : null,
            coAuthorIds: isset($data['co_author_ids']) && is_array($data['co_author_ids']) ? array_map('intval', $data['co_author_ids']) : [],
        );
    }
}
