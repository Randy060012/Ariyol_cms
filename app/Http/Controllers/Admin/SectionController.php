<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Section;
use App\Services\PageRendererService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SectionController extends Controller
{
    public function store(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:'.implode(',', array_keys(PageRendererService::SECTION_TYPES))],
        ]);

        $page->sections()->create([
            'type' => $validated['type'],
            'data' => [],
            'sort_order' => $page->sections()->count(),
        ]);

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('success', 'Section ajoutée. Complétez son contenu.');
    }

    public function edit(Page $page, Section $section): View
    {
        $fields = PageRendererService::sectionTypeFields($section->type);
        $imageField = $this->imageField($section->type);

        return view('admin.sections.form', [
            'page' => $page,
            'section' => $section,
            'fields' => $fields,
            'imageField' => $imageField,
            'action' => route('admin.sections.update', [$page, $section]),
        ]);
    }

    public function update(Request $request, Page $page, Section $section): RedirectResponse
    {
        $fields = PageRendererService::sectionTypeFields($section->type);
        $imageField = $this->imageField($section->type);

        $rules = [];
        foreach ($fields as $field => $config) {
            $rules["data.{$field}"] = ['nullable', 'string', 'max:6000'];
        }

        $validated = $request->validate($rules);

        $data = PageRendererService::buildData($section->type, $validated['data'] ?? []);

        $this->handleImage($request, $section, $data, $imageField);

        // Galeries multi-images (logos partenaires, images de section, ...).
        foreach ($this->galleryFields($section->type) as $galleryField) {
            $this->handleGallery($request, $section, $data, $galleryField);
        }

        $section->update([
            'data' => $data,
            'is_visible' => $request->boolean('is_visible'),
        ]);

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('success', 'Section mise à jour.');
    }

    public function destroy(Page $page, Section $section): RedirectResponse
    {
        $section->delete();

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('success', 'Section supprimée.');
    }

    /**
     * Name of the image field for a section type, if any.
     */
    private function imageField(string $type): ?string
    {
        foreach (PageRendererService::sectionTypeFields($type) as $name => $config) {
            if (($config['type'] ?? 'text') === 'image') {
                return $name;
            }
        }

        return null;
    }

    /**
     * Resolve the image value for storage, handling the three cases:
     * - a file is uploaded: store it and replace the previous one;
    * - the removal checkbox is checked: delete the current image;
     * - otherwise: keep the current image (uploads must not be lost when
     *   the admin edits other fields without re-selecting a file).
     */
    private function handleImage(Request $request, Section $section, array &$data, ?string $imageField): void
    {
        if ($imageField === null) {
            return;
        }

        $current = $section->field($imageField);

        if ($request->hasFile("data_file_{$imageField}")) {
            $request->validate([
                "data_file_{$imageField}" => ['image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
            ]);

            $this->deleteStoredImage($current);

            $path = $request->file("data_file_{$imageField}")->store('sections', 'public');
            $data[$imageField] = 'storage/'.$path;

            return;
        }

        if ($request->boolean("remove_{$imageField}")) {
            $this->deleteStoredImage($current);
            $data[$imageField] = null;

            return;
        }

        // No upload, no removal: preserve the existing image, if any.
        if (is_string($current) && $current !== '') {
            $data[$imageField] = $current;
        }
    }

    /**
     * Names of the multi-image (gallery) fields for a section type, if any.
     */
    private function galleryFields(string $type): array
    {
        $fields = [];

        foreach (PageRendererService::sectionTypeFields($type) as $name => $config) {
            if (($config['type'] ?? 'text') === 'gallery') {
                $fields[] = $name;
            }
        }

        return $fields;
    }

    /**
     * Resolve a gallery value (array of image paths) for storage, handling
     * the three cases:
     * - files are uploaded: store each one and append them to the gallery;
     * - images are checked for removal: delete them from storage;
     * - otherwise: keep the current images (uploads must not be lost when
     *   the admin edits other fields without re-selecting files).
     */
    private function handleGallery(Request $request, Section $section, array &$data, string $field): void
    {
        $current = $section->field($field, []);

        if (! is_array($current)) {
            $current = [];
        }

        // Remove the images checked in the form (identified by their index).
        $removed = array_map('intval', (array) $request->input("remove_{$field}", []));
        $kept = [];

        foreach ($current as $index => $path) {
            if (in_array($index, $removed, true)) {
                $this->deleteStoredImage($path);

                continue;
            }

            $kept[] = $path;
        }

        // Append newly uploaded images, after validating each file.
        if ($request->hasFile("data_file_{$field}")) {
            $request->validate([
                "data_file_{$field}" => ['array'],
                "data_file_{$field}.*" => ['image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
            ]);

            foreach ($request->file("data_file_{$field}") as $file) {
                $kept[] = 'storage/'.$file->store('sections', 'public');
            }
        }

        if ($kept !== []) {
            $data[$field] = $kept;
        }
    }

    /**
     * Delete a previously uploaded image from the public disk (leaves
     * external URLs and bundled assets untouched).
     */
    private function deleteStoredImage(?string $value): void
    {
        if (! is_string($value) || ! str_starts_with($value, 'storage/')) {
            return;
        }

        \Illuminate\Support\Facades\Storage::disk('public')->delete(substr($value, strlen('storage/')));
    }

    public function move(Request $request, Page $page, Section $section): RedirectResponse
    {
        $validated = $request->validate([
            'direction' => ['required', 'in:up,down'],
        ]);

        $query = $page->sections()->orderBy('sort_order');
        $sections = $query->get();
        $index = $sections->search(fn ($s) => $s->id === $section->id);
        $swapIndex = $validated['direction'] === 'up' ? $index - 1 : $index + 1;

        if ($index !== false && isset($sections[$swapIndex])) {
            $sections[$swapIndex]->update(['sort_order' => $index]);
            $section->update(['sort_order' => $swapIndex]);
        }

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('success', 'Ordre des sections modifié.');
    }
}
