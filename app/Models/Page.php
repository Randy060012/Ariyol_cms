<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'page_key',
        'hero_kicker',
        'hero_title',
        'hero_subtitle',
        'hero_image',
        'meta_title',
        'meta_description',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('sort_order');
    }

    public function visibleSections(): HasMany
    {
        return $this->sections()->where('is_visible', true);
    }

    /**
     * Find a page by its stable key (home, about, programmes...).
     */
    public static function forKey(string $key): ?self
    {
        if (! Schema::hasTable('pages')) {
            return null;
        }

        return static::where('page_key', $key)->first();
    }

    /**
     * Find a published page by slug, or by key when the slug matches.
     */
    public static function findPublishedBySlug(string $slug): ?self
    {
        if (! Schema::hasTable('pages')) {
            return null;
        }

        return static::query()
            ->where('is_published', true)
            ->where(function ($query) use ($slug) {
                $query->where('slug', $slug)->orWhere('page_key', $slug);
            })
            ->first();
    }
}
