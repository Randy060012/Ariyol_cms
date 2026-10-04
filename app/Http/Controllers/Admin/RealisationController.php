<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Realisation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RealisationController extends Controller
{
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
        // Validate files first so a rejected upload never persists a partial item.
        $this->validateImage($request);

        $realisation = Realisation::create($this->validated($request));

        $this->handleImage($request, $realisation);

        return redirect()
            ->route('admin.realisations.edit', $realisation)
            ->with('success', 'Réalisation créée. Vous pouvez ajouter la photo.');
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
        $this->validateImage($request);

        $realisation->update($this->validated($request));

        $this->handleImage($request, $realisation);

        return redirect()
            ->route('admin.realisations.edit', $realisation)
            ->with('success', 'Réalisation mise à jour.');
    }

    public function destroy(Realisation $realisation): RedirectResponse
    {
        $this->deleteStored($realisation->image);

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
            'description' => ['nullable', 'string', 'max:60000'],
            'date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:160'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ]) + [
            'is_published' => $request->boolean('is_published'),
        ];
    }

    /* ------------------------------------------------------------------
     |  Image lifecycle
     * ------------------------------------------------------------------ */

    private function validateImage(Request $request): void
    {
        $request->validate([
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
        ]);
    }

    /**
     * Image: upload wins, remove-checkbox clears it, otherwise preserved.
     */
    private function handleImage(Request $request, Realisation $realisation): void
    {
        if ($request->hasFile('image_file')) {
            $this->deleteStored($realisation->image);

            $path = $request->file('image_file')->store('realisations', 'public');
            $realisation->forceFill(['image' => 'storage/'.$path])->save();

            return;
        }

        if ($request->boolean('remove_image')) {
            $this->deleteStored($realisation->image);
            $realisation->forceFill(['image' => null])->save();
        }

        // No upload, no removal: keep the current image.
    }

    private function deleteStored(?string $value): void
    {
        if (! is_string($value) || ! str_starts_with($value, 'storage/')) {
            return;
        }

        Storage::disk('public')->delete(substr($value, strlen('storage/')));
    }
}
