<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchContactReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_request_id',
        'user_id',
        'message',
    ];

    /**
     * Get the parent contact inquiry request.
     *
     * @return BelongsTo<ResearchContactRequest, $this>
     */
    public function contactRequest(): BelongsTo
    {
        return $this->belongsTo(ResearchContactRequest::class, 'contact_request_id');
    }

    /**
     * Get the user who sent this reply.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
