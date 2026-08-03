<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchCitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_id',
        'cited_by_research_id',
        'citation_type',
    ];

    /**
     * Get the paper that is cited.
     *
     * @return BelongsTo<Research, $this>
     */
    public function research(): BelongsTo
    {
        return $this->belongsTo(Research::class, 'research_id');
    }

    /**
     * Get the paper making the citation.
     *
     * @return BelongsTo<Research, $this>
     */
    public function citedByResearch(): BelongsTo
    {
        return $this->belongsTo(Research::class, 'cited_by_research_id');
    }
}
