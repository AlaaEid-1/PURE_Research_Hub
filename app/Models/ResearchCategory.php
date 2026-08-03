<?php

namespace App\Models;

use App\Services\ResearchCategoryService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResearchCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Get the research publications belonging to this category.
     *
     * @return HasMany<Research, $this>
     */
    public function researches(): HasMany
    {
        return $this->hasMany(Research::class, 'category_id');
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::saved(function () {
            app(ResearchCategoryService::class)->clearCache();
        });

        static::deleted(function () {
            app(ResearchCategoryService::class)->clearCache();
        });
    }
}
