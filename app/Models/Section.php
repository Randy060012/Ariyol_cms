<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Section extends Model
{
    protected $table = 'page_sections';
    protected $fillable = [
        'page_id',
        'type',
        'data',
        'sort_order',
        'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_visible' => 'boolean',
    ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get a value from the JSON payload with dot notation support.
     */
    public function field(string $key, mixed $default = null): mixed
    {
        return data_get($this->data, $key, $default);
    }

    /**
     * Convert the items array back to editable "Title | Description" lines.
     */
    public function itemsAsText(): string
    {
        $items = $this->field('items', []);

        if (! is_array($items)) {
            return (string) $items;
        }

        return collect($items)
            ->map(fn ($item) => trim(($item['title'] ?? '').' | '.($item['description'] ?? '')))
            ->implode("\n");
    }
}
