@extends('admin.layouts.app')

@section('title', $page->exists ? 'Modifier la page' : 'Créer une page')

@section('content')

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
        @csrf
        @if ($page->exists)
            @method('PUT')
        @endif

        <div class="panel">
            <h2>Informations générales</h2>

            <div class="field">
                <label for="title">Titre de la page <span class="hint">Utilisé dans l'administration et comme titre par défaut.</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $page->title) }}" required>
                @error('title') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label for="slug">Slug (URL) <span class="hint">Laisser vide pour le générer depuis le titre. Lettres minuscules et tirets.</span></label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $page->slug) }}">
                @error('slug') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label>
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $page->exists ? $page->is_published : true))>
                    Page publiée (visible sur le site public)
                </label>
            </div>

            <div class="field">
                <label for="sort_order">Ordre d'affichage <span class="hint">Nombre entier, les plus petits apparaissent en premier dans l'administration.</span></label>
                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $page->sort_order ?? 0) }}" min="0">
            </div>
        </div>

        <div class="panel">
            <h2>En-tête de page (hero)</h2>

            <div class="field">
                <label for="hero_kicker">Sur-titre <span class="hint">Petit texte en majuscules au-dessus du titre.</span></label>
                <input type="text" id="hero_kicker" name="hero_kicker" value="{{ old('hero_kicker', $page->hero_kicker) }}">
            </div>

            <div class="field">
                <label for="hero_title">Titre principal</label>
                <input type="text" id="hero_title" name="hero_title" value="{{ old('hero_title', $page->hero_title) }}">
            </div>

            <div class="field">
                <label for="hero_subtitle">Sous-titre</label>
                <textarea id="hero_subtitle" name="hero_subtitle" rows="2">{{ old('hero_subtitle', $page->hero_subtitle) }}</textarea>
            </div>

            <div class="field">
                <label for="hero_image">Image de fond <span class="hint">URL externe ou chemin interne (ex. storage/pages/xxx.jpg). Laisser vide pour un en-tête uni.</span></label>
                <input type="text" id="hero_image" name="hero_image" value="{{ old('hero_image', $page->hero_image) }}">
                @error('hero_image') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label for="hero_image_file">... ou téléverser une image</label>
                <input type="file" id="hero_image_file" name="hero_image_file" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                @error('hero_image_file') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            @if ($page->hero_image)
                <div style="margin-top: 8px;">
                    <img src="{{ asset($page->hero_image) }}" alt="Image actuelle" style="max-height: 120px; border: 1px solid var(--border);">
                </div>
            @endif
        </div>

        <div class="panel">
            <h2>Référencement (SEO)</h2>

            <div class="field">
                <label for="meta_title">Titre SEO</label>
                <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}">
            </div>

            <div class="field">
                <label for="meta_description">Description SEO</label>
                <textarea id="meta_description" name="meta_description" rows="3">{{ old('meta_description', $page->meta_description) }}</textarea>
            </div>
        </div>

        <div style="display: flex; gap: 12px;">
            <button type="submit" class="btn btn-green">{{ $page->exists ? 'Mettre à jour' : 'Créer la page' }}</button>
            <a href="{{ route('admin.pages.index') }}" class="btn btn-ghost">Annuler</a>
        </div>
    </form>

    @if ($page->exists)
        <div class="panel" style="margin-top: 24px;">
            <h2>Sections de la page</h2>
            <p class="panel-sub">Les sections constituent le contenu de la page, dans l'ordre.</p>

            @forelse ($page->sections as $section)
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--border);">
                    <div>
                        <strong>{{ \App\Services\PageRendererService::SECTION_TYPES[$section->type] ?? $section->type }}</strong>
                        <span class="badge {{ $section->is_visible ? 'badge-green' : '' }}">{{ $section->is_visible ? 'Visible' : 'Masquée' }}</span>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <form method="POST" action="{{ route('admin.sections.move', [$page, $section]) }}">
                            @csrf
                            <input type="hidden" name="direction" value="up">
                            <button type="submit" class="btn btn-ghost btn-sm">Monter</button>
                        </form>
                        <form method="POST" action="{{ route('admin.sections.move', [$page, $section]) }}">
                            @csrf
                            <input type="hidden" name="direction" value="down">
                            <button type="submit" class="btn btn-ghost btn-sm">Descendre</button>
                        </form>
                        <a href="{{ route('admin.sections.edit', [$page, $section]) }}" class="btn btn-primary btn-sm">Modifier</a>
                        <form method="POST" action="{{ route('admin.sections.destroy', [$page, $section]) }}" onsubmit="return confirm('Supprimer cette section ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="panel-sub">Aucune section pour l'instant.</p>
            @endforelse

            <form method="POST" action="{{ route('admin.sections.store', $page) }}" style="display: flex; gap: 12px; margin-top: 18px;">
                @csrf
                <select name="type" required>
                    <option value="">Ajouter une section...</option>
                    @foreach (\App\Services\PageRendererService::SECTION_TYPES as $type => $label)
                        <option value="{{ $type }}">{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-green">Ajouter</button>
            </form>
        </div>
    @endif

@endsection
