<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesMediaUploads;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Section;
use App\Services\PageRendererService;
use App\Support\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SectionController extends Controller
{
    use HandlesMediaUploads;

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

        return view('admin.sections.form', [
            'page' => $page,
            'section' => $section,
            'fields' => $fields,
            'itemImagesAllowed' => PageRendererService::supportsItemImages($section->type),
            'action' => route('admin.sections.update', [$page, $section]),
        ]);
    }

    public function update(Request $request, Page $page, Section $section): RedirectResponse
    {
        $fields = PageRendererService::sectionTypeFields($section->type);

        $data = PageRendererService::buildData($section->type, $this->validated($request, $fields));

        // Single images (one per section type).
        foreach (PageRendererService::imageFields($section->type) as $field) {
            $data[$field] = $this->resolveImage(
                $request,
                "data_file_{$field}",
                "remove_{$field}",
                $section->field($field),
                PageRendererService::SECTION_FOLDER,
                "data.{$field}",
            );
        }

        // Galleries multi-images (logos partenaires, images de section, ...).
        foreach (PageRendererService::galleryFields($section->type) as $field) {
            $data[$field] = $this->resolveGallery(
                $request,
                "data_file_{$field}",
                "remove_{$field}",
                $section->gallery($field),
                PageRendererService::SECTION_FOLDER,
            );
        }

        // Photo par carte.
        if (PageRendererService::supportsItemImages($section->type)) {
            $data['items'] = PageRendererService::attachItemImages(
                $data['items'] ?? [],
                $this->resolveItemImages($request, $section),
            );
        }

        $section->update([
            'data' => $this->prune($data),
            'is_visible' => $request->boolean('is_visible'),
        ]);

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('success', 'Section mise à jour.');
    }

    public function destroy(Page $page, Section $section): RedirectResponse
    {
        Media::deleteStoredAll($section->storedMedia());

        $section->delete();

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('success', 'Section supprimée.');
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

    /**
     * Validate the text fields of a section, per its type.
     */
    private function validated(Request $request, array $fields): array
    {
        $rules = [];

        foreach ($fields as $field => $config) {
            $type = $config['type'] ?? PageRendererService::FIELD_TEXT;

            // Image and gallery fields are handled by the upload pipeline.
            if (in_array($type, [PageRendererService::FIELD_IMAGE, PageRendererService::FIELD_GALLERY], true)) {
                continue;
            }

            $rules["data.{$field}"] = $type === PageRendererService::FIELD_NUMBER
                ? ['nullable', 'integer', 'in:1,2,3']
                : ['nullable', 'string', 'max:20000'];
        }

        return $request->validate($rules)['data'] ?? [];
    }

    /**
     * Resolve the photos attached to individual cards, indexed by position.
     *
     * @return array<int, string>
     */
    private function resolveItemImages(Request $request, Section $section): array
    {
        $current = $section->itemImages();
        $images = [];

        foreach ($section->items() as $index => $item) {
            $fileKey = "data_item_image_{$index}";
            $removeKey = "remove_item_image_{$index}";

            $existing = $current[$index] ?? null;

            if ($request->hasFile($fileKey)) {
                $this->validateSingleUpload($request, $fileKey);

                Media::deleteStored($existing);
                $images[$index] = Media::store($request->file($fileKey), PageRendererService::SECTION_FOLDER);

                continue;
            }

            if ($request->boolean($removeKey)) {
                Media::deleteStored($existing);

                continue;
            }

            if ($existing !== null) {
                $images[$index] = $existing;
            }
        }

        return $images;
    }

    /**
     * Drop empty values so the JSON payload stays light and predictable.
     */
    private function prune(array $data): array
    {
        return array_filter(
            $data,
            fn ($value) => $value !== null && $value !== '' && $value !== []
        );
    }
}
