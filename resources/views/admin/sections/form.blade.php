@extends('admin.layouts.app')

@section('title', 'Modifier la section')

@section('content')

    <div class="panel">
        <h2>{{ \App\Services\PageRendererService::SECTION_TYPES[$section->type] ?? $section->type }}</h2>
        <p class="panel-sub">Page : {{ $page->title }}</p>

        <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="field">
                <label>
                    <input type="checkbox" name="is_visible" value="1" @checked(old('is_visible', $section->is_visible))>
                    Section visible sur le site public
                </label>
            </div>

            @foreach ($fields as $name => $config)
                @php
                    $fieldType = $config['type'] ?? \App\Services\PageRendererService::FIELD_TEXT;
                    $currentValue = old('data.'.$name, $section->field($name));
                    $inputId = 'data_'.$name;
                @endphp

                @if ($fieldType === \App\Services\PageRendererService::FIELD_TEXTAREA)
                    <div class="field">
                        <label for="{{ $inputId }}">{{ $config['label'] }}</label>
                        @if ($name === 'items')
                            <textarea id="{{ $inputId }}" name="data[{{ $name }}]" rows="{{ $config['rows'] ?? 4 }}">{{ $section->itemsAsText() }}</textarea>
                        @else
                            <textarea id="{{ $inputId }}" name="data[{{ $name }}]" rows="{{ $config['rows'] ?? 4 }}">{{ $currentValue }}</textarea>
                        @endif
                    </div>
                @elseif ($fieldType === \App\Services\PageRendererService::FIELD_IMAGE)
                    <div class="field">
                        <label for="{{ $inputId }}">{{ $config['label'] }} <span class="hint">Une image principale, affichée en haut de la section. Laissez le champ vide si vous téléversez un fichier.</span></label>
                        <input type="text" id="{{ $inputId }}" name="data[{{ $name }}]" value="{{ $currentValue }}">

                        <label for="data_file_{{ $name }}" style="margin-top: 8px;">... ou téléverser une image</label>
                        <input type="file" id="data_file_{{ $name }}" name="data_file_{{ $name }}" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                        @error('data_file_'.$name) <span class="error-text">{{ $message }}</span> @enderror

                        @if (!empty($currentValue))
                            <div class="media-current">
                                <img src="{{ \App\Support\Media::url($currentValue) }}" alt="Image actuelle">
                                <label style="font-weight: 400;">
                                    <input type="checkbox" name="remove_{{ $name }}" value="1">
                                    Supprimer cette image
                                </label>
                            </div>
                        @endif
                    </div>
                @elseif ($fieldType === \App\Services\PageRendererService::FIELD_GALLERY)
                    @php $currentImages = old('data.'.$name, $section->gallery($name)); @endphp

                    <div class="field">
                        <label>{{ $config['label'] }} <span class="hint">Téléversez plusieurs fichiers d'un coup si besoin. Les images existantes sont conservées tant qu'elles ne sont pas cochées pour suppression.</span></label>

                        @if (count($currentImages))
                            <div class="gallery-current">
                                @foreach ($currentImages as $i => $img)
                                    <div class="gallery-current-item">
                                        <img src="{{ \App\Support\Media::url($img) }}" alt="Image {{ $i + 1 }}">
                                        <label style="font-weight: 400;">
                                            <input type="checkbox" name="remove_{{ $name }}[]" value="{{ $i }}">
                                            Supprimer
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <label for="data_file_{{ $name }}" style="margin-top: 8px;">... ou ajouter des images <span class="hint">Sélection multiple ou glisser-déposer.</span></label>
                        <input type="file" id="data_file_{{ $name }}" name="data_file_{{ $name }}[]" multiple accept="image/png,image/jpeg,image/webp,image/svg+xml">
                        @error('data_file_'.$name) <span class="error-text">{{ $message }}</span> @enderror
                        @error('data_file_'.$name.'.*') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                @else
                    <div class="field">
                        <label for="{{ $inputId }}">{{ $config['label'] }}</label>
                        <input type="{{ $fieldType }}" id="{{ $inputId }}" name="data[{{ $name }}]" value="{{ $currentValue }}">
                    </div>
                @endif
            @endforeach

            @if ($itemImagesAllowed)
                @php $itemImages = $section->itemImages(); @endphp

                <div class="field">
                    <label>Photo de chaque carte <span class="hint">Optionnel. Les photos suivent l'ordre des lignes saisies ci-dessus et remplacent la carte sans photo.</span></label>

                    @if (count($section->items()))
                        <div class="item-media-list">
                            @foreach ($section->items() as $index => $item)
                                @php $itemImage = $itemImages[$index] ?? null; @endphp

                                <div class="item-media">
                                    <div class="item-media-head">
                                        <strong>{{ $index + 1 }}. {{ $item['title'] ?? 'Carte sans titre' }}</strong>
                                    </div>

                                    @if ($itemImage)
                                        <div class="media-current">
                                            <img src="{{ \App\Support\Media::url($itemImage) }}" alt="Photo de la carte {{ $index + 1 }}">
                                            <label style="font-weight: 400;">
                                                <input type="checkbox" name="remove_item_image_{{ $index }}" value="1">
                                                Supprimer cette photo
                                            </label>
                                        </div>
                                    @endif

                                    <input type="file" id="data_item_image_{{ $index }}" name="data_item_image_{{ $index }}" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                                    @error('data_item_image_'.$index) <span class="error-text">{{ $message }}</span> @enderror
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="hint">Ajoutez d'abord des cartes dans le champ de texte, puis revenez pour leur associer une photo.</p>
                    @endif
                </div>
            @endif

            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn btn-green">Enregistrer</button>
                <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-ghost">Retour à la page</a>
            </div>
        </form>
    </div>

@endsection

@push('styles')
    {{-- FilePond : CSS core + plugin aperçus (identique aux articles) --}}
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

            /* ---- Galeries de section (images ou logos) : multiple, réordonnable ---- */
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

            /* ---- Images unitaires : image de section et photos de carte ---- */
            document.querySelectorAll('input[type="file"]:not([multiple])').forEach(function (input) {
                attach(input, {
                    allowMultiple: false,
                    maxFiles: 1,
                    maxFileSize: '4MB',
                    acceptedFileTypes: acceptedFileTypes
                });
            });
        });
    </script>
@endpush