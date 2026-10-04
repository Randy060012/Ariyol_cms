<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesMediaUploads;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageController extends Controller
{
    use HandlesMediaUploads;

    /**
     * Storage folder for the hero images of a page.
     */
    private const HERO_FOLDER = 'pages';

    public function index(): View
    {
        $pages = Page::query()
            ->withCount('sections')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('admin.pages.index', compact('pages'));
    }

    public function create(): View
    {
        return view('admin.pages.form', [
            'page' => new Page,
            'action' => route('admin.pages.store'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Validate uploads first so a rejected file never persists a partial page.
        $this->validateHeroUploads($request);

        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($request->input('slug') ?: $request->input('title'));
        $data['page_key'] = $this->uniquePageKey($data['slug']);

        $page = Page::create($data);

        $this->handleHeroUploads($request, $page);

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('success', 'Page créée. Vous pouvez maintenant ajouter des sections.');
    }

    public function edit(Page $page): View
    {
        $page->load('sections');

        return view('admin.pages.form', [
            'page' => $page,
            'action' => route('admin.pages.update', $page),
        ]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $this->validateHeroUploads($request);

        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug(
            $request->input('slug') ?: $request->input('title'),
            $page->id
        );

        $page->update($data);

        $this->handleHeroUploads($request, $page);

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('success', 'Page mise à jour.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        if (in_array($page->page_key, ['home', 'about', 'programmes', 'realisations', 'contact'], true)) {
            return back()->with('error', 'Les pages essentielles du site ne peuvent pas être supprimées.');
        }

        Media::deleteStoredAll($page->heroMedia());

        // Sections carry their own uploaded files.
        foreach ($page->sections as $section) {
            Media::deleteStoredAll($section->storedMedia());
        }

        $page->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Page supprimée.');
    }

    /**
     * Text fields of a page. Media fields are handled separately.
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'hero_kicker' => ['nullable', 'string', 'max:255'],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:1000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ]) + [
            'is_published' => $request->boolean('is_published'),
        ];
    }

    private function validateHeroUploads(Request $request): void
    {
        $this->validateSingleUpload($request, 'hero_image_file');

        $request->validate([
            'hero_gallery_files' => ['nullable', 'array', 'max:12'],
            'hero_gallery_files.*' => Media::uploadRules(),
        ]);
    }

    /**
     * Hero cover image and hero gallery.
     *
     * Upload wins, removal checkbox clears, otherwise the current value is
     * preserved so editing another field never drops an upload.
     */
    private function handleHeroUploads(Request $request, Page $page): void
    {
        $page->forceFill([
            'hero_image' => $this->resolveImage(
                $request,
                'hero_image_file',
                'remove_hero_image',
                $page->hero_image,
                self::HERO_FOLDER,
                'hero_image',
            ),
            'hero_images' => $this->resolveGallery(
                $request,
                'hero_gallery_files',
                'remove_hero_gallery',
                $page->heroGallery(),
                self::HERO_FOLDER,
            ),
        ])->save();
    }

    /**
     * Custom pages receive a generated, stable page_key (core pages keep theirs).
     */
    private function uniquePageKey(string $slug): string
    {
        $base = 'custom-'.$slug;
        $key = $base;
        $i = 2;

        while (Page::where('page_key', $key)->exists()) {
            $key = $base.'-'.$i++;
        }

        return $key;
    }

    private function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = Str::slug($base) ?: 'page';
        $original = $slug;
        $i = 2;

        while (
            Page::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = $original.'-'.$i++;
        }

        return $slug;
    }
}
