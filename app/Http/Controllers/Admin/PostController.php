<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesMediaUploads;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    use HandlesMediaUploads;

    /**
     * Storage folder for the images of an article.
     */
    private const POST_FOLDER = 'posts';

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
            'post' => new Post,
            'action' => route('admin.posts.store'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Validate uploads first so a rejected file never persists a partial article.
        $this->validateImageUploads($request);

        $data = $this->validated($request);

        $data['slug'] = Post::uniqueSlug($request->input('slug') ?: $request->input('title'));

        $post = Post::create($data);

        $this->handleImageUploads($request, $post);

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
        $this->validateImageUploads($request);

        $data = $this->validated($request);

        $data['slug'] = Post::uniqueSlug(
            $request->input('slug') ?: $request->input('title'),
            $post->id
        );

        $post->update($data);

        $this->handleImageUploads($request, $post);

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('success', 'Article mis à jour.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        Media::deleteStoredAll(array_merge([$post->main_image], $post->galleryImages()));

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
            'author' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'string', 'max:80'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ]) + [
            'is_published' => $request->boolean('is_published'),
        ];

        // The main image is resolved by the upload pipeline, never by the text field.
        return $data;
    }

    /**
     * Validate both image inputs up front (store and update share these rules).
     */
    private function validateImageUploads(Request $request): void
    {
        $this->validateSingleUpload($request, 'main_image_file');

        $request->validate([
            'gallery_files' => ['nullable', 'array', 'max:12'],
            'gallery_files.*' => Media::uploadRules(),
        ]);
    }

    /**
     * Main image + gallery of an article.
     */
    private function handleImageUploads(Request $request, Post $post): void
    {
        $post->forceFill([
            'main_image' => $this->resolveImage(
                $request,
                'main_image_file',
                'remove_main_image',
                $post->main_image,
                self::POST_FOLDER,
                'main_image',
            ),
            'gallery' => $this->resolveGallery(
                $request,
                'gallery_files',
                'gallery_remove',
                $post->galleryImages(),
                self::POST_FOLDER,
            ),
        ])->save();
    }
}
