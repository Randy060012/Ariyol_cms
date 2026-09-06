<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\PageRendererService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
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
            'page' => new Page(),
            'action' => route('admin.pages.store'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($request->input('slug') ?: $request->input('title'));
        $data['page_key'] = $this->uniquePageKey($data['slug']);

        $page = Page::create($data);

        $this->handleHeroUpload($request, $page);

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('success', 'Page créée. Vous pouvez maintenant ajouter des sections.');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.form', [
            'page' => $page,
            'action' => route('admin.pages.update', $page),
        ]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug(
            $request->input('slug') ?: $request->input('title'),
            $page->id
        );

        $page->update($data);

        $this->handleHeroUpload($request, $page);

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('success', 'Page mise à jour.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        if (in_array($page->page_key, ['home', 'about', 'programmes', 'realisations', 'contact'], true)) {
            return back()->with('error', 'Les pages essentielles du site ne peuvent pas être supprimées.');
        }

        $page->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Page supprimée.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'hero_kicker' => ['nullable', 'string', 'max:255'],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:1000'],
            'hero_image' => ['nullable', 'string', 'max:1000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ]) + [
            'is_published' => $request->boolean('is_published'),
        ];
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
        $slug = \Illuminate\Support\Str::slug($base) ?: 'page';
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

    private function handleHeroUpload(Request $request, Page $page): void
    {
        if (! $request->hasFile('hero_image_file')) {
            return;
        }

        $path = $request->file('hero_image_file')->store('pages', 'public');

        $page->forceFill(['hero_image' => 'storage/'.$path])->save();
    }
}
