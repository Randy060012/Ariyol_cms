<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Centralise the handling of media paths coming from the CMS.
 *
 * A media value is either:
 * - an absolute URL (https://...) typed by hand in the admin: kept as-is;
 * - a relative public path such as "storage/sections/abc.jpg";
 * - a bundled asset such as "images/logo.svg".
 */
class Media
{
    /**
     * Detect browser captures accidentally uploaded as site photos.
     *
     * The development media folder contains full-page screenshots (rather
     * than field photography) at these exact capture sizes. Hiding those
     * files lets the public site fall back to clearly labelled illustrations.
     */
    public static function isBrowserCapture(?string $value): bool
    {
        if (! self::isStored($value)) {
            return false;
        }

        static $captureCache = [];
        $value = (string) $value;

        if (array_key_exists($value, $captureCache)) {
            return $captureCache[$value];
        }

        $path = substr((string) $value, strlen(self::STORED_PREFIX));

        if (! Storage::disk('public')->exists($path)) {
            return $captureCache[$value] = false;
        }

        $contents = Storage::disk('public')->get($path);
        $image = @getimagesizefromstring($contents);

        if (! is_array($image)) {
            return $captureCache[$value] = false;
        }

        return $captureCache[$value] = in_array([$image[0], $image[1]], [
            [1763, 899],
            [1763, 952],
            [1883, 653],
            [1763, 2923],
            [1763, 4486],
        ], true);
    }

    /**
     * Public disk prefix used for every uploaded file.
     */
    public const STORED_PREFIX = 'storage/';

    /**
     * Validation rules shared by every image upload field of the admin.
     *
     * @return array<int, string>
     */
    public static function uploadRules(): array
    {
        return ['image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'];
    }

    /**
     * Turn a stored media value into a browsable URL.
     *
     * Absolute URLs are returned untouched, so a hand-typed external image
     * never ends up prefixed with the application host.
     */
    public static function url(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (str_starts_with($value, '//') || preg_match('#^[a-z][a-z0-9+.\-]*://#i', $value) === 1) {
            return $value;
        }

        return asset($value);
    }

    /**
     * Same as url() but always returns a string, for inline styles.
     */
    public static function style(?string $value): string
    {
        return (string) self::url($value);
    }

    /**
     * Store an uploaded file on the public disk and return its media value.
     */
    public static function store(UploadedFile $file, string $folder): string
    {
        return self::STORED_PREFIX.$file->store($folder, 'public');
    }

    /**
     * Store several uploaded files on the public disk.
     *
     * @param  array<int, UploadedFile>  $files
     * @return array<int, string>
     */
    public static function storeMany(array $files, string $folder): array
    {
        return array_values(array_map(
            fn (UploadedFile $file) => self::store($file, $folder),
            $files
        ));
    }

    /**
     * Is this media value a file we uploaded (as opposed to a URL or asset)?
     */
    public static function isStored(?string $value): bool
    {
        return is_string($value) && str_starts_with($value, self::STORED_PREFIX);
    }

    /**
     * Delete an uploaded file from the public disk.
     *
     * External URLs and bundled assets are never touched.
     */
    public static function deleteStored(?string $value): void
    {
        if (! self::isStored($value)) {
            return;
        }

        Storage::disk('public')->delete(substr($value, strlen(self::STORED_PREFIX)));
    }

    /**
     * Delete every uploaded file of a list.
     *
     * @param  array<int, string|null>  $values
     */
    public static function deleteStoredAll(array $values): void
    {
        foreach ($values as $value) {
            self::deleteStored(is_string($value) ? $value : null);
        }
    }

    /**
     * Clean a list of media values: drop empty entries, re-index.
     *
     * @param  mixed  $values
     * @return array<int, string>
     */
    public static function clean($values): array
    {
        return array_values(array_filter(
            (array) $values,
            fn ($value) => is_string($value) && trim($value) !== ''
        ));
    }
}
