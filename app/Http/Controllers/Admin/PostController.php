<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::query()
            ->ordered()
            ->get();

        return view('admin.posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('admin.posts.form', [
            'post' => new Post(),
            'action' => route('admin.posts.store'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Validate files first so a rejected upload never persists a partial article.
        $this->validateImages($request);

        $data = $this->validated($request);

        $data['slug'] = Post::uniqueSlug($request->input('slug') ?: $request->input('title'));

        $post = Post::create($data);

        $this->handleImages($request, $post);

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('success', 'Article créé. Vous pouvez ajouter les images de la galerie.');
    }

    public function edit(Post $post): View
    {
        return view('admin.posts.form', [
            'post' => $post,
            'action' => route('admin.posts.update', $post),
        ]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $data = $this->validated($request);

        $data['slug'] = Post::uniqueSlug(
            $request->input('slug') ?: $request->input('title'),
            $post->id
        );

        $post->update($data);

        $this->handleImages($request, $post);

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('success', 'Article mis à jour.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->deleteStored($post->main_image);
        $this->deleteStoredAll($post->galleryImages());

        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Article supprimé.');
    }

    /* ------------------------------------------------------------------
     |  Validation
     * ------------------------------------------------------------------ */

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string', 'max:60000'],
            'main_image' => ['nullable', 'string', 'max:1000'],
            'author' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'string', 'max:80'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ]) + [
            'is_published' => $request->boolean('is_published'),
        ];

        // Never let the text field wipe the stored main image.
        unset($data['main_image']);

        return $data;
    }

    /* ------------------------------------------------------------------
     |  Image lifecycle: main image + gallery
     * ------------------------------------------------------------------ */

    /**
     * Validate both image inputs up front (store and update share these rules).
     */
    private function validateImages(Request $request): void
    {
        $request->validate([
            'main_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
            'gallery_files' => ['nullable', 'array', 'max:12'],
            'gallery_files.*' => ['image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
        ]);
    }

    private function handleImages(Request $request, Post $post): void
    {
        $this->handleMainImage($request, $post);
        $this->handleGallery($request, $post);
    }

    /**
     * Main image: upload wins, remove-checkbox clears it, otherwise preserved.
     */
    private function handleMainImage(Request $request, Post $post): void
    {
        if ($request->hasFile('main_image_file')) {
            $this->deleteStored($post->main_image);

            $path = $request->file('main_image_file')->store('posts', 'public');
            $post->forceFill(['main_image' => 'storage/'.$path])->save();

            return;
        }

        if ($request->boolean('remove_main_image')) {
            $this->deleteStored($post->main_image);
            $post->forceFill(['main_image' => null])->save();
        }

        // No upload, no removal: keep the current image.
    }

    /**
     * Gallery: appends uploaded files, honors per-image removal checkboxes
     * (gallery_remove[<index>]) and preserves untouched existing entries.
     */
    private function handleGallery(Request $request, Post $post): void
    {
        $existing = $post->galleryImages();
        $remove = (array) $request->input('gallery_remove', []);

        $kept = [];

        foreach ($existing as $index => $path) {
            if (isset($remove[$index])) {
                $this->deleteStored($path);
                continue;
            }

            $kept[] = $path;
        }

        if ($request->hasFile('gallery_files')) {
            foreach ($request->file('gallery_files') as $file) {
                $path = $file->store('posts', 'public');
                $kept[] = 'storage/'.$path;
            }
        }

        $post->forceFill(['gallery' => $kept])->save();
    }

    /* ------------------------------------------------------------------
     |  Storage helpers (external URLs and bundled assets are never touched)
     * ------------------------------------------------------------------ */

    private function deleteStored(?string $value): void
    {
        if (! is_string($value) || ! str_starts_with($value, 'storage/')) {
            return;
        }

        Storage::disk('public')->delete(substr($value, strlen('storage/')));
    }

    private function deleteStoredAll(array $values): void
    {
        foreach ($values as $value) {
            $this->deleteStored($value);
        }
    }
}
