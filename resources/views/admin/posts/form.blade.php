@extends('admin.layouts.app')

@section('title', $post->exists ? 'Modifier l\'article' : 'Écrire un article')

@section('content')

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
        @csrf
        @if ($post->exists)
            @method('PUT')
        @endif

        <div class="panel">
            <h2>Article</h2>

            <div class="field">
                <label for="title">Titre <span class="req" style="color: var(--green);">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" required>
                @error('title') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label for="slug">Slug (URL) <span class="hint">Laisser vide pour le générer depuis le titre. ex. mon-article</span></label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $post->slug) }}">
                @error('slug') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label for="excerpt">Résumé <span class="hint">Court texte affiché sur les cartes du blog et dans les partages.</span></label>
                <textarea id="excerpt" name="excerpt" rows="3" maxlength="500">{{ old('excerpt', $post->excerpt) }}</textarea>
                @error('excerpt') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label for="content">Contenu <span class="hint">Paragraphes séparés par une ligne vide. Une ligne simple reste dans le même paragraphe.</span></label>
                <textarea id="content" name="content" rows="14">{{ old('content', $post->content) }}</textarea>
                @error('content') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                <div class="field" style="margin-bottom: 0;">
                    <label for="author">Auteur</label>
                    <input type="text" id="author" name="author" value="{{ old('author', $post->author ?? 'AFRIYOL') }}">
                    @error('author') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="field" style="margin-bottom: 0;">
                    <label for="category">Catégorie <span class="hint">ex. Environnement, Paix, Jeunesse</span></label>
                    <input type="text" id="category" name="category" value="{{ old('category', $post->category) }}" list="categories">
                    <datalist id="categories">
                        <option value="Environnement">
                        <option value="Paix & Droits Humains">
                        <option value="Éducation">
                        <option value="Entrepreneuriat">
                        <option value="Vie associative">
                    </datalist>
                    @error('category') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="panel">
            <h2>Image principale</h2>
            <p class="panel-sub">Mise en avant sur la carte de l'article et en haut de la page de lecture.</p>

            <div class="field">
                <label for="main_image">Image (URL ou chemin)</label>
                <input class="form-control" type="text" id="main_image" name="main_image" value="{{ old('main_image', $post->main_image) }}" placeholder="https://... ou storage/posts/xxx.jpg">
                @error('main_image') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label for="main_image_file">... ou téléverser une image</label>
                <input type="file" id="main_image_file" name="main_image_file" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                @error('main_image_file') <span class="error-text">{{ $message }}</span> @enderror
                @if ($post->main_image)
                    <span class="hint">Laisser ce champ vide conserve l'image actuelle.</span>
                @endif
            </div>

            @if ($post->main_image)
                <div style="margin: 8px 0 16px;">
                    <img src="{{ asset($post->main_image) }}" alt="Image principale actuelle" style="max-height: 160px; border: 1px solid var(--border);">
                </div>
                <div class="field">
                    <label>
                        <input type="checkbox" name="remove_main_image" value="1">
                        Supprimer l'image principale
                    </label>
                </div>
            @endif
        </div>

        <div class="panel">
            <h2>Galerie d'images</h2>
            <p class="panel-sub">Images additionnelles affichées dans l'article. Sélection multiple possible.</p>

            @if ($post->galleryImages())
                <div class="gallery-existing">
                    @foreach ($post->galleryImages() as $index => $path)
                        <figure>
                            <img src="{{ asset($path) }}" alt="Image de galerie {{ $index + 1 }}">
                            <label>
                                <input type="checkbox" name="gallery_remove[{{ $index }}]" value="1">
                                Supprimer
                            </label>
                        </figure>
                    @endforeach
                </div>
            @endif

            <div class="field">
                <label for="gallery_files">Ajouter des images <span class="hint">Sélection multiple ou glisser-déposer. Réordonnez les images par glisser-déposer.</span></label>
                <input type="file" id="gallery_files" name="gallery_files[]" accept="image/png,image/jpeg,image/webp,image/svg+xml" multiple>
                @error('gallery_files.*') <span class="error-text">{{ $message }}</span> @enderror
                @error('gallery_files') <span class="error-text">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="panel">
            <h2>Publication</h2>

            <div class="field">
                <label>
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $post->exists ? $post->is_published : true))>
                    Article publié (visible sur le blog)
                </label>
            </div>

            <div class="field">
                <label for="sort_order">Ordre d'affichage <span class="hint">À date égale, les plus petits numéros apparaissent en premier.</span></label>
                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $post->sort_order ?? 0) }}" min="0">
            </div>
        </div>

        <div style="display: flex; gap: 12px;">
            <button type="submit" class="btn btn-green">{{ $post->exists ? 'Mettre à jour' : 'Publier l\'article' }}</button>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-ghost">Annuler</a>
        </div>
    </form>

@endsection

@push('styles')
    {{-- FilePond : CSS core + plugin aperçus --}}
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

        /* Images existantes de la galerie */
        .gallery-existing { display: flex; flex-wrap: wrap; gap: 16px; margin-bottom: 18px; }
        .gallery-existing figure { width: 150px; margin: 0; }
        .gallery-existing img { width: 150px; height: 100px; object-fit: cover; border: 1px solid var(--border); }
        .gallery-existing label { display: flex; gap: 6px; align-items: center; font-size: 12px; margin-top: 6px; font-weight: 400; }
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

            /* ---- Galerie : multiple, réordonnable, synchronisée avec le champ natif ---- */
            var galleryInput = document.querySelector('#gallery_files');
            if (galleryInput) {
                var galleryPond = FilePond.create(galleryInput, {
                    allowMultiple: true,
                    maxFiles: 12,
                    maxFileSize: '4MB',
                    acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'],
                    allowReorder: true,
                    itemInsertLocation: 'end',
                    credits: false,
                    ...fr
                });

                /* À la soumission : le FileList natif est reconstruit dans l'ordre
                   d'affichage (et vidé si l'admin a tout retiré de la file). */
                var galleryForm = galleryInput.closest('form');
                if (galleryForm) {
                    galleryForm.addEventListener('submit', function () {
                        var dt = new DataTransfer();
                        galleryPond.getFiles().forEach(function (f) { dt.items.add(f.file); });
                        galleryInput.files = dt.files;
                    });
                }
            }

            /* ---- Image principale : fichier unique ---- */
            var mainInput = document.querySelector('#main_image_file');
            if (mainInput) {
                var mainPond = FilePond.create(mainInput, {
                    allowMultiple: false,
                    maxFiles: 1,
                    maxFileSize: '4MB',
                    acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'],
                    credits: false,
                    ...fr
                });

                var mainForm = mainInput.closest('form');
                if (mainForm) {
                    mainForm.addEventListener('submit', function () {
                        var dt = new DataTransfer();
                        var files = mainPond.getFiles();
                        if (files.length) { dt.items.add(files[0].file); }
                        mainInput.files = dt.files;
                    });
                }
            }
        });
    </script>
@endpush
