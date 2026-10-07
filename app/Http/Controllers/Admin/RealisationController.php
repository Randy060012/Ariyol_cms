<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Concerns\HandlesMediaUploads;
use App\Models\Realisation;
use App\Support\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RealisationController extends Controller
{
    use HandlesMediaUploads;

    public function index(): View
    {
        $realisations = Realisation::query()
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.realisations.index', compact('realisations'));
    }

    public function create(): View
    {
        return view('admin.realisations.form', [
            'realisation' => new Realisation(),
            'action' => route('admin.realisations.store'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validateUploads($request);

        $data = $this->validated($request);
        $data['slug'] = Realisation::uniqueSlug($request->input('slug') ?: $request->input('title'));
        $realisation = Realisation::create($data);

        $this->handleUploads($request, $realisation);

        return redirect()
            ->route('admin.realisations.edit', $realisation)
            ->with('success', 'Réalisation créée. Vous pouvez compléter sa galerie.');
    }

    public function edit(Realisation $realisation): View
    {
        return view('admin.realisations.form', [
            'realisation' => $realisation,
            'action' => route('admin.realisations.update', $realisation),
        ]);
    }

    public function update(Request $request, Realisation $realisation): RedirectResponse
    {
        $this->validateUploads($request);

        $data = $this->validated($request);
        $data['slug'] = Realisation::uniqueSlug($request->input('slug') ?: $request->input('title'), $realisation->id);
        $realisation->update($data);

        $this->handleUploads($request, $realisation);

        return redirect()
            ->route('admin.realisations.edit', $realisation)
            ->with('success', 'Réalisation mise à jour.');
    }

    public function destroy(Realisation $realisation): RedirectResponse
    {
        Media::deleteStoredAll(array_merge([$realisation->image], $realisation->galleryImages()));

        $realisation->delete();

        return redirect()
            ->route('admin.realisations.index')
            ->with('success', 'Réalisation supprimée.');
    }

    /* ------------------------------------------------------------------
     |  Validation
     * ------------------------------------------------------------------ */

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'description' => ['nullable', 'string', 'max:60000'],
            'content' => ['nullable', 'string', 'max:60000'],
            'image' => ['nullable', 'string', 'max:2048'],
            'date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:160'],
            'category' => ['nullable', 'string', 'max:100'],
            'impact' => ['nullable', 'string', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ]) + [
            'is_published' => $request->boolean('is_published'),
        ];
    }

    /* ------------------------------------------------------------------
     |  Image lifecycle
     * ------------------------------------------------------------------ */

    private function validateUploads(Request $request): void
    {
        $this->validateSingleUpload($request, 'image_file');
        $request->validate([
            'gallery_files' => ['nullable', 'array', 'max:12'],
            'gallery_files.*' => Media::uploadRules(),
        ]);
    }

    /**
     * Image: upload wins, remove-checkbox clears it, otherwise preserved.
     */
    private function handleUploads(Request $request, Realisation $realisation): void
    {
        $realisation->forceFill([
            'image' => $this->resolveImage($request, 'image_file', 'remove_image', $realisation->image, 'realisations', 'image'),
            'gallery' => $this->resolveGallery($request, 'gallery_files', 'gallery_remove', $realisation->galleryImages(), 'realisations'),
        ])->save();
    }
}
