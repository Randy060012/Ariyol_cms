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
                @php $currentValue = old('data.'.$name, $section->field($name)); @endphp

                @if (($config['type'] ?? 'text') === 'textarea')
                    <div class="field">
                        <label for="data[{{ $name }}]">{{ $config['label'] }}</label>
                        <textarea id="data[{{ $name }}]" name="data[{{ $name }}]" rows="{{ $config['rows'] ?? 4 }}">{{ $section->type === 'facts' || $section->type === 'cards' || $section->type === 'checklist' ? $section->itemsAsText() : $currentValue }}</textarea>
                    </div>
                @elseif (($config['type'] ?? 'text') === 'image')
                    <div class="field">
                        <label for="data[{{ $name }}]">{{ $config['label'] }} <span class="hint">Laisser vide si vous téléversez un fichier ci-dessous.</span></label>
                        <input type="text" id="data[{{ $name }}]" name="data[{{ $name }}]" value="{{ $currentValue }}">

                        <label for="data_file_{{ $name }}" style="margin-top: 8px;">... ou téléverser une image</label>
                        <input type="file" id="data_file_{{ $name }}" name="data_file_{{ $name }}" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                        @error('data_file_'.$name) <span class="error-text">{{ $message }}</span> @enderror

                        @if (!empty($currentValue))
                            <div style="margin-top: 10px; display: flex; align-items: center; gap: 14px;">
                                <img src="{{ asset($currentValue) }}" alt="Image actuelle" style="max-height: 110px; border: 1px solid var(--border);">
                                <label style="font-weight: 400;">
                                    <input type="checkbox" name="remove_{{ $name }}" value="1">
                                    Supprimer cette image
                                </label>
                            </div>
                        @endif
                    </div>
                @elseif (($config['type'] ?? 'text') === 'gallery')
                    @php
                        $currentImages = old('data.'.$name, $section->field($name, []));
                        if (! is_array($currentImages)) {
                            $currentImages = [];
                        }
                    @endphp

                    <div class="field">
                        <label>{{ $config['label'] }} <span class="hint">Téléversez plusieurs fichiers d'un coup si besoin. Les images existantes sont conservées tant qu'elles ne sont pas cochées pour suppression.</span></label>

                        @if (count($currentImages))
                            <div class="gallery-current">
                                @foreach ($currentImages as $i => $img)
                                    <div class="gallery-current-item">
                                        <img src="{{ asset($img) }}" alt="Image {{ $i + 1 }}">
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
                        @error('data_file_'.$name.'.*') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                @else
                    <div class="field">
                        <label for="data[{{ $name }}]">{{ $config['label'] }}</label>
                        <input type="{{ $config['type'] ?? 'text' }}" id="data[{{ $name }}]" name="data[{{ $name }}]" value="{{ $currentValue }}">
                    </div>
                @endif
            @endforeach

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

            /* ---- Galeries de section (images ou logos) : multiple, réordonnable ---- */
            document.querySelectorAll('input[type="file"][name^="data_file_"][multiple]').forEach(function (input) {
                var pond = FilePond.create(input, {
                    allowMultiple: true,
                    maxFiles: 12,
                    maxFileSize: '4MB',
                    acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'],
                    allowReorder: true,
                    itemInsertLocation: 'end',
                    credits: false,
                    ...fr
                });

                var form = input.closest('form');
                if (form) {
                    form.addEventListener('submit', function () {
                        var dt = new DataTransfer();
                        pond.getFiles().forEach(function (f) { dt.items.add(f.file); });
                        input.files = dt.files;
                    });
                }
            });
        });
    </script>
@endpush
