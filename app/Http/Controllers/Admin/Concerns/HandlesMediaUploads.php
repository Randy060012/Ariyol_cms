<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * Shared upload lifecycle for the CMS image fields.
 *
 * Every image field follows the same three cases, so an admin never loses an
 * upload by editing another field:
 * - a file is uploaded: it is stored and replaces the previous value;
 * - the removal box is ticked: the current file is deleted;
 * - otherwise the current value is preserved.
 */
trait HandlesMediaUploads
{
    /**
     * Resolve the value of a single-image field.
     *
     * @param  string  $fileKey  input name of the file field (e.g. data_file_image)
     * @param  string  $removeKey  input name of the removal checkbox
     * @param  string  $textKey  input name of the "URL or path" text field, if any
     */
    protected function resolveImage(
        Request $request,
        string $fileKey,
        string $removeKey,
        ?string $current,
        string $folder,
        ?string $textKey = null,
    ): ?string {
        $submitted = $textKey ? trim((string) $request->input($textKey)) : '';

        if ($request->hasFile($fileKey)) {
            $this->validateSingleUpload($request, $fileKey);

            Media::deleteStored($current);

            return Media::store($request->file($fileKey), $folder);
        }

        if ($request->boolean($removeKey)) {
            Media::deleteStored($current);

            return null;
        }

        // A hand-typed URL or path replaces the current image.
        if ($submitted !== '' && $submitted !== $current) {
            Media::deleteStored($current);

            return $submitted;
        }

        return $current;
    }

    /**
     * Resolve the value of a multi-image (gallery) field.
     *
     * Removal checkboxes are submitted as remove_<field>[] with the index of
     * the image as value. Removed images are deleted from storage, new uploads
     * are appended, and untouched images keep their original order.
     *
     * @param  array<int, string>  $current
     * @return array<int, string>
     */
    protected function resolveGallery(
        Request $request,
        string $fileKey,
        string $removeKey,
        array $current,
        string $folder,
        int $max = 12,
    ): array {
        $removed = array_map('intval', array_values((array) $request->input($removeKey, [])));

        $kept = [];

        foreach (array_values($current) as $index => $path) {
            if (in_array($index, $removed, true)) {
                Media::deleteStored($path);

                continue;
            }

            $kept[] = $path;
        }

        foreach ($this->uploadedFiles($request, $fileKey, $max) as $file) {
            $kept[] = Media::store($file, $folder);
        }

        return $kept;
    }

    /**
     * Validate and return the uploaded files of a field, if any.
     *
     * @return array<int, UploadedFile>
     */
    protected function uploadedFiles(Request $request, string $key, int $max = 12): array
    {
        if (! $request->hasFile($key)) {
            return [];
        }

        $request->validate([
            $key => ['array', "max:{$max}"],
            $key.'.*' => Media::uploadRules(),
        ]);

        return array_values(array_filter(
            $request->file($key),
            fn ($file) => $file instanceof UploadedFile
        ));
    }

    /**
     * Validate a single uploaded file field.
     */
    protected function validateSingleUpload(Request $request, string $key): void
    {
        $request->validate([
            $key => array_merge(['nullable'], Media::uploadRules()),
        ]);
    }
}
