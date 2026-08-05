<?php

namespace App\Models;

use App\Enums\Role;
use App\Traits\HasAvatar;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasAvatar, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'institution',
        'department',
        'bio',
        'research_interests',
        'orcid_id',
        'google_scholar_url',
        'website_url',
        'avatar_path',
        'role',
        'is_verified_researcher',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be appended to model arrays.
     *
     * @var list<string>
     */
    protected $appends = [
        'avatar_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'is_verified_researcher' => 'boolean',
        ];
    }

    /**
     * Check if the user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->role === Role::ADMIN;
    }

    /**
     * Check if the user is a researcher.
     */
    public function isResearcher(): bool
    {
        return $this->role === Role::USER;
    }

    /**
     * Research papers owned/created by the user.
     *
     * @return HasMany<Research, $this>
     */
    public function researches(): HasMany
    {
        return $this->hasMany(Research::class);
    }

    /**
     * Research papers co-authored by the user.
     *
     * @return BelongsToMany<Research, $this>
     */
    public function authoredResearches(): BelongsToMany
    {
        return $this->belongsToMany(Research::class, 'research_authors')
            ->withPivot('author_order')
            ->withTimestamps();
    }

    /**
     * Access requests sent by this user.
     *
     * @return HasMany<ResearchAccessRequest, $this>
     */
    public function sentAccessRequests(): HasMany
    {
        return $this->hasMany(ResearchAccessRequest::class, 'requester_id');
    }

    /**
     * Access requests received for papers owned by this user.
     *
     * @return HasManyThrough<ResearchAccessRequest, Research, $this>
     */
    public function receivedAccessRequests(): HasManyThrough
    {
        return $this->hasManyThrough(ResearchAccessRequest::class, Research::class, 'user_id', 'research_id');
    }

    /**
     * Contact inquiry requests sent by this user.
     *
     * @return HasMany<ResearchContactRequest, $this>
     */
    public function contactRequests(): HasMany
    {
        return $this->hasMany(ResearchContactRequest::class, 'sender_id');
    }

    /**
     * Conversations initiated by this user.
     *
     * @return HasMany<Conversation, $this>
     */
    public function sentConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'sender_id');
    }

    /**
     * Conversations received by this author.
     *
     * @return HasMany<Conversation, $this>
     */
    public function receivedConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'author_id');
    }

    /**
     * Messages sent by this user.
     *
     * @return HasMany<Message, $this>
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Research access grants issued to this user.
     *
     * @return HasMany<ResearchAccessGrant, $this>
     */
    public function accessGrants(): HasMany
    {
        return $this->hasMany(ResearchAccessGrant::class, 'user_id');
    }

    /**
     * Bookmarked/saved research papers collection.
     *
     * @return BelongsToMany<Research, $this>
     */
    public function savedResearches(): BelongsToMany
    {
        return $this->belongsToMany(Research::class, 'saved_researches')->withTimestamps();
    }
}
