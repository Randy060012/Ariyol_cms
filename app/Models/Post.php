<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'main_image',
        'gallery',
        'author',
        'category',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'is_published' => 'boolean',
        ];
    }

    /* ------------------------------------------------------------------
     |  Relations & helpers
     * ------------------------------------------------------------------ */

    /**
     * Gallery images without nulls, re-indexed.
     */
    public function galleryImages(): array
    {
        return array_values(array_filter((array) $this->gallery, fn ($v) => is_string($v) && $v !== ''));
    }

    /**
     * Paragraphes du corps : séparés par une ligne vide dans l'éditeur.
     */
    public function paragraphs(): array
    {
        return array_values(array_filter(
            preg_split('/\n{2,}/', trim((string) $this->content) ?: '') ?: [],
            fn ($p) => trim($p) !== ''
        ));
    }

    /**
     * Reading time estimate (200 words per minute).
     */
    public function readingTime(): int
    {
        $words = str_word_count(strip_tags((string) $this->content.' '.(string) $this->excerpt));

        return max(1, (int) ceil($words / 200));
    }

    public function formattedDate(): string
    {
        return $this->created_at?->isoFormat('D MMMM YYYY') ?? '';
    }

    /* ------------------------------------------------------------------
     |  Scopes
     * ------------------------------------------------------------------ */

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Latest published posts first; sort_order asc as tiebreaker.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('created_at')->orderBy('sort_order');
    }

    /* ------------------------------------------------------------------
     |  Slug handling
     * ------------------------------------------------------------------ */

    public static function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = Str::slug($base) ?: 'article';
        $original = $slug;
        $i = 2;

        while (
            static::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = $original.'-'.$i++;
        }

        return $slug;
    }

    /**
     * Table guard so nothing breaks when migrations have not run yet.
     */
    public static function tableExists(): bool
    {
        return Schema::hasTable('posts');
    }
}
