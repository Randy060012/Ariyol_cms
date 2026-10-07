<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Realisation extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'image',
        'gallery',
        'date',
        'location',
        'category',
        'impact',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_published' => 'boolean',
            'gallery' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Realisation $realisation): void {
            if (! $realisation->slug && $realisation->title) {
                $realisation->slug = static::uniqueSlug($realisation->title, $realisation->exists ? $realisation->id : null);
            }
        });
    }

    /* ------------------------------------------------------------------
     |  Helpers
     * ------------------------------------------------------------------ */

    public function formattedDate(): string
    {
        return $this->date?->isoFormat('D MMMM YYYY') ?? '';
    }

    public function paragraphs(): array
    {
        return array_values(array_filter(
            preg_split('/\n{2,}/', trim((string) ($this->content ?: $this->description)) ?: '') ?: [],
            fn ($paragraph) => trim($paragraph) !== '',
        ));
    }

    public function galleryImages(): array
    {
        return array_values(array_filter((array) $this->gallery, fn ($image) => is_string($image) && $image !== ''));
    }

    public static function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = Str::slug($base) ?: 'realisation';
        $original = $slug;
        $suffix = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = $original.'-'.$suffix++;
        }

        return $slug;
    }

    /* ------------------------------------------------------------------
     |  Scopes
     * ------------------------------------------------------------------ */

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Actions datées d'abord (les plus récentes), non datées ensuite ;
     * sort_order sert de départage.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByRaw('date IS NULL, date DESC')->orderBy('sort_order');
    }

    /**
     * Realisations for public display (limit 0 = all published).
     */
    public static function forDisplay(int $limit = 0): Collection
    {
        if (! static::tableExists()) {
            return collect();
        }

        $query = static::published()->ordered();

        if ($limit > 0) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Table guard so nothing breaks when migrations have not run yet.
     */
    public static function tableExists(): bool
    {
        return Schema::hasTable('realisations');
    }
}
