<?php

namespace App\Models;

use App\Enums\DownloadPermission;
use App\Enums\ResearchStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Research extends Model
{
    use HasFactory;

    protected $table = 'researches';

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'abstract',
        'keywords',
        'doi',
        'publication_date',
        'pdf_path',
        'thumbnail_path',
        'copyright_information',
        'download_permission',
        'views',
        'downloads',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'publication_date' => 'date',
            'views' => 'integer',
            'downloads' => 'integer',
            'download_permission' => DownloadPermission::class,
            'status' => ResearchStatus::class,
        ];
    }

    /**
     * Get the owner user of this research publication.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of this research publication.
     *
     * @return BelongsTo<ResearchCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ResearchCategory::class, 'category_id');
    }

    /**
     * Get the co-authors of this research publication.
     *
     * @return BelongsToMany<User, $this>
     */
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'research_authors')
            ->withPivot('author_order')
            ->withTimestamps()
            ->orderBy('research_authors.author_order', 'asc');
    }

    /**
     * Get the access requests submitted for this research paper.
     *
     * @return HasMany<ResearchAccessRequest, $this>
     */
    public function accessRequests(): HasMany
    {
        return $this->hasMany(ResearchAccessRequest::class);
    }

    /**
     * Get the contact inquiry requests submitted for this research paper.
     *
     * @return HasMany<ResearchContactRequest, $this>
     */
    public function contactRequests(): HasMany
    {
        return $this->hasMany(ResearchContactRequest::class);
    }

    /**
     * Get conversations created for this research paper.
     *
     * @return HasMany<Conversation, $this>
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Get access grants issued for this research paper.
     *
     * @return HasMany<ResearchAccessGrant, $this>
     */
    public function accessGrants(): HasMany
    {
        return $this->hasMany(ResearchAccessGrant::class);
    }

    /**
     * Get the download analytics log records for this paper.
     *
     * @return HasMany<ResearchDownloadLog, $this>
     */
    public function downloadLogs(): HasMany
    {
        return $this->hasMany(ResearchDownloadLog::class);
    }

    /**
     * Get the citations referencing this paper.
     *
     * @return HasMany<ResearchCitation, $this>
     */
    public function citations(): HasMany
    {
        return $this->hasMany(ResearchCitation::class, 'research_id');
    }

    /**
     * Get the citations made by this paper.
     *
     * @return HasMany<ResearchCitation, $this>
     */
    public function citedResearches(): HasMany
    {
        return $this->hasMany(ResearchCitation::class, 'cited_by_research_id');
    }

    /**
     * Get users who have bookmarked/saved this paper.
     *
     * @return BelongsToMany<User, $this>
     */
    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saved_researches')->withTimestamps();
    }

    /**
     * Get the URL for the thumbnail or null if none exists.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (!empty($this->thumbnail_path)) {
            return \Illuminate\Support\Facades\Storage::disk('avatars')->url($this->thumbnail_path);
        }

        return null;
    }
}
