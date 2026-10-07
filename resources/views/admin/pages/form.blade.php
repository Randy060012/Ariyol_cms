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
                <label for="hero_image">Photo principale de la page <span class="hint">Elle illustre le bandeau à côté du titre. URL externe ou média téléversé dans le CMS.</span></label>
                <input type="text" id="hero_image" name="hero_image" value="{{ old('hero_image', $page->hero_image) }}">
            </div>

            <div class="field">
                <label for="hero_image_file">... ou téléverser une photo</label>
                <input type="file" id="hero_image_file" name="hero_image_file" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                @error('hero_image_file') <span class="error-text">{{ $message }}</span> @enderror

                @if ($page->hero_image)
                    <div class="media-current">
                        <img src="{{ \App\Support\Media::url($page->hero_image) }}" alt="Photo principale actuelle">
                        <label style="font-weight: 400;">
                            <input type="checkbox" name="remove_hero_image" value="1">
                            Supprimer la photo principale
                        </label>
                    </div>
                @endif
            </div>

            @php $heroGallery = $page->heroGallery(); @endphp

            <div class="field">
                <label for="hero_gallery_files">Galerie de l'en-tête <span class="hint">Images affichées en bandeau sous le titre de la page. Sélection multiple ou glisser-déposer.</span></label>

                @if (count($heroGallery))
                    <div class="gallery-current">
                        @foreach ($heroGallery as $i => $img)
                            <div class="gallery-current-item">
                                <img src="{{ \App\Support\Media::url($img) }}" alt="Image de l'en-tête {{ $i + 1 }}">
                                <label style="font-weight: 400;">
                                    <input type="checkbox" name="remove_hero_gallery[]" value="{{ $i }}">
                                    Supprimer
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif

                <label for="hero_gallery_files" style="margin-top: 8px;">... ou ajouter des images</label>
                <input type="file" id="hero_gallery_files" name="hero_gallery_files[]" multiple accept="image/png,image/jpeg,image/webp,image/svg+xml">
                @error('hero_gallery_files') <span class="error-text">{{ $message }}</span> @enderror
                @error('hero_gallery_files.*') <span class="error-text">{{ $message }}</span> @enderror
            </div>
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
                @php
                    $thumb = $section->field('image') ?: ($section->gallery('images')[0] ?? null);
                @endphp

                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--border);">
                    <div class="section-summary">
                        @if ($thumb)
                            <img class="section-thumb" src="{{ \App\Support\Media::url($thumb) }}" alt="">
                        @endif
                        <div>
                            <div class="section-summary-meta">
                                <strong>{{ \App\Services\PageRendererService::SECTION_TYPES[$section->type] ?? $section->type }}</strong>
                                <span class="badge {{ $section->is_visible ? 'badge-green' : '' }}">{{ $section->is_visible ? 'Visible' : 'Masquée' }}</span>
                            </div>
                            @if ($label = $section->field('title'))
                                <span class="hint" style="font-size: 12.5px; color: var(--muted);">{{ $label }}</span>
                            @endif
                        </div>
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

@push('styles')
    {{-- FilePond : CSS core + plugin aperçus (identique aux sections et aux articles) --}}
    <link href="https://cdn.jsdelivr.net/npm/filepond@4.25.2/dist/filepond.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/filepond-plugin-image-preview@4.6.12/dist/filepond-plugin-image-preview.min.css" rel="stylesheet">
    <style>
        /* FilePond : palette alignée sur la charte admin (navy / green / bordures) */
        .filepond--root { margin-bottom: 0; font-family: var(--font); }
        .filepond--panel-root {
            background-color: #f8fafc;
            border: 1px dashed var(--border);
            border-radius: 2px;
        }
        .filepond--drop-label { color: var(--muted); font-size: 13px; }
        .filepond--label-action { text-decoration-color: var(--green); }
        .filepond--item-panel { background-color: var(--navy); border-radius: 2px; }
        .filepond--file { color: var(--white); }
        .filepond--drip-blob { background-color: var(--green); }
    </style>
@endpush

@push('scripts')
    {{-- FilePond : JS core + plugins (type, taille, aperçus, orientation EXIF) --}}
    <script src="https://cdn.jsdelivr.net/npm/filepond@4.25.2/dist/filepond.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/filepond-plugin-file-validate-type@1.2.9/dist/filepond-plugin-file-validate-type.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/filepond-plugin-file-validate-size@1.0.6/dist/filepond-plugin-file-validate-size.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/filepond-plugin-image-preview@4.6.12/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/filepond-plugin-image-exif-orientation@1.0.11/dist/filepond-plugin-image-exif-orientation.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof FilePond === 'undefined') { return; } // fallback : input natif intact

            FilePond.registerPlugin(
                FilePondPluginFileValidateType,
                FilePondPluginFileValidateSize,
                FilePondPluginImagePreview,
                FilePondPluginImageExifOrientation
            );

            var fr = {
                labelIdle: 'Glissez vos images ici ou <span class="filepond--label-action">parcourez</span>',
                labelFileWaitingForSize: 'En attente de taille',
                labelFileSizeNotAvailable: 'Taille non disponible',
                labelFileLoading: 'Chargement',
                labelFileLoadError: 'Erreur pendant le chargement',
                labelImagePreview: 'Aperçu',
                labelFileProcessingComplete: 'Prête',
                labelTapToCancel: 'toucher pour annuler',
                labelTapToRetry: 'toucher pour réessayer',
                labelTapToUndo: 'toucher pour annuler',
                labelButtonRemoveItem: 'Retirer',
                labelFileTypeNotAllowed: 'Type de fichier non autorisé',
                fileValidateTypeLabelExpectedTypes: 'Formats attendus : JPEG, PNG, WebP ou SVG',
                labelMaxFileSizeExceeded: 'Fichier trop volumineux',
                labelMaxFileSize: 'La taille maximale est de 4 Mo'
            };

            var acceptedFileTypes = ['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'];

            /* Chaque champ est converti au premier FilePond détecté : le FileList
               natif est reconstruit à la soumission pour rester compatible. */
            function attach(input, options) {
                var pond = FilePond.create(input, Object.assign({
                    credits: false,
                    ...fr
                }, options));

                var form = input.closest('form');
                if (! form) { return; }

                form.addEventListener('submit', function () {
                    var dt = new DataTransfer();
                    pond.getFiles().forEach(function (f) { dt.items.add(f.file); });
                    input.files = dt.files;
                });
            }

            /* ---- Galeries : multiple, réordonnable ---- */
            document.querySelectorAll('input[type="file"][multiple]').forEach(function (input) {
                attach(input, {
                    allowMultiple: true,
                    maxFiles: 12,
                    maxFileSize: '4MB',
                    acceptedFileTypes: acceptedFileTypes,
                    allowReorder: true,
                    itemInsertLocation: 'end'
                });
            });

            /* ---- Image de fond : fichier unique ---- */
            var heroInput = document.querySelector('#hero_image_file');
            if (heroInput) {
                attach(heroInput, {
                    allowMultiple: false,
                    maxFiles: 1,
                    maxFileSize: '4MB',
                    acceptedFileTypes: acceptedFileTypes
                });
            }
        });
    </script>
@endpush
