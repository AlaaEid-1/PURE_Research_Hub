<?php

namespace App\Models;

use App\Enums\ContactRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchContactRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_id',
        'sender_id',
        'subject',
        'message',
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
            'status' => ContactRequestStatus::class,
        ];
    }

    /**
     * Get the target research publication.
     *
     * @return BelongsTo<Research, $this>
     */
    public function research(): BelongsTo
    {
        return $this->belongsTo(Research::class);
    }

    /**
     * Get the user sending the contact inquiry.
     *
     * @return BelongsTo<User, $this>
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get all replies in this conversation thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ResearchContactReply, $this>
     */
    public function replies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ResearchContactReply::class, 'contact_request_id')->orderBy('created_at', 'asc');
    }

    /**
     * Helper accessor for author user.
     */
    public function getAuthorAttribute(): ?User
    {
        return $this->research?->user;
    }
}
