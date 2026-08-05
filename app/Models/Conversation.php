<?php

namespace App\Models;

use App\Enums\ConversationStatus;
use App\Enums\ConversationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_id',
        'author_id',
        'sender_id',
        'guest_name',
        'guest_email',
        'type',
        'subject',
        'status',
        'last_message_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ConversationType::class,
            'status' => ConversationStatus::class,
            'last_message_at' => 'datetime',
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
     * Get the author user (owner of the research paper).
     *
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the sender user (null for guests).
     *
     * @return BelongsTo<User, $this>
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get all messages in this conversation thread.
     *
     * @return HasMany<Message, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the latest message in the conversation.
     *
     * @return HasOne<Message, $this>
     */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Count unread messages for a specific user.
     */
    public function unreadCount(?User $user): int
    {
        if (! $user) {
            return 0;
        }

        return $this->messages()
            ->where(function ($query) use ($user) {
                $query->whereNull('sender_id')
                    ->orWhere('sender_id', '!=', $user->id);
            })
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Check if user is a participant in this conversation.
     */
    public function isParticipant(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return (int) $user->id === (int) $this->author_id 
            || (int) $user->id === (int) $this->sender_id;
    }
}
