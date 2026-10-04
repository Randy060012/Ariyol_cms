<?php

namespace App\Models;

use App\Support\Media;
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

    /**
     * Section items, always as an array of rows.
     *
     * @return array<int, array<string, mixed>>
     */
    public function items(): array
    {
        $items = $this->field('items', []);

        return is_array($items) ? array_values($items) : [];
    }

    /**
     * Per-item photos, indexed by item position.
     *
     * @return array<int, string>
     */
    public function itemImages(): array
    {
        $images = [];

        foreach ($this->items() as $index => $item) {
            $image = $item['image'] ?? null;

            if (is_string($image) && $image !== '') {
                $images[$index] = $image;
            }
        }

        return $images;
    }

    /**
     * Section gallery, without nulls, re-indexed.
     *
     * @return array<int, string>
     */
    public function gallery(string $field): array
    {
        return Media::clean($this->field($field, []));
    }

    /**
     * Every uploaded file of the section, for cleanup on deletion.
     *
     * @return array<int, string>
     */
    public function storedMedia(): array
    {
        $values = array_merge(
            $this->gallery('images'),
            $this->gallery('logos'),
            [$this->field('image')],
            array_values($this->itemImages()),
        );

        return $values;
    }
}
