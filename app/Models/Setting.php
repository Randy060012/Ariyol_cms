<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $table = 'site_settings';

    protected $fillable = ['key', 'value', 'type', 'group'];

    /**
     * Retrieve a setting value by key with a default fallback.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        $settings = static::allCached();

        return $settings[$key] ?? $default;
    }

    /**
     * Insert or update a setting value.
     */
    public static function set(string $key, ?string $value, string $type = 'text', string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => $group]
        );

        static::flushCache();
    }

    /**
     * All settings as key => value, cached.
     */
    public static function allCached(): array
    {
        if (! Schema::hasTable('site_settings')) {
            return [];
        }

        return Cache::rememberForever('site_settings', function () {
            return static::query()->pluck('value', 'key')->all();
        });
    }

    public static function flushCache(): void
    {
        Cache::forget('site_settings');
    }
}
