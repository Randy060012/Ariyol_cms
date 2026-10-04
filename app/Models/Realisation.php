<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class Realisation extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'date',
        'location',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_published' => 'boolean',
        ];
    }

    /* ------------------------------------------------------------------
     |  Helpers
     * ------------------------------------------------------------------ */

    public function formattedDate(): string
    {
        return $this->date?->isoFormat('D MMMM YYYY') ?? '';
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
