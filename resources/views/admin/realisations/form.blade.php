@extends('admin.layouts.app')

@section('title', $realisation->exists ? 'Modifier la réalisation' : 'Ajouter une réalisation')

@section('content')

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
        @csrf
        @if ($realisation->exists)
            @method('PUT')
        @endif

        <div class="panel">
            <h2>Réalisation</h2>

            <div class="field">
                <label for="title">Titre <span style="color: var(--green);">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $realisation->title) }}" required>
                @error('title') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label for="description">Texte <span class="hint">Affiché à droite de la photo. Décrivez l'action, son déroulé et ses résultats.</span></label>
                <textarea id="description" name="description" rows="8">{{ old('description', $realisation->description) }}</textarea>
                @error('description') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                <div class="field" style="margin-bottom: 0;">
                    <label for="date">Date de l'action <span class="hint">Affichée en sur-titre de la réalisation.</span></label>
                    <input type="date" id="date" name="date" value="{{ old('date', $realisation->date?->format('Y-m-d')) }}">
                    @error('date') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="field" style="margin-bottom: 0;">
                    <label for="location">Lieu <span class="hint">ex. Tsévié, Zéglé-Sagonou</span></label>
                    <input type="text" id="location" name="location" value="{{ old('location', $realisation->location) }}">
                    @error('location') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="panel">
            <h2>Photo</h2>
            <p class="panel-sub">Affichée à gauche de la réalisation. Idéalement paysage (format 4:3 ou 3:2).</p>

            <div class="field">
                <label for="image_file">Téléverser une image</label>
                <input type="file" id="image_file" name="image_file" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                @error('image_file') <span class="error-text">{{ $message }}</span> @enderror
                @if ($realisation->image)
                    <span class="hint">Laisser ce champ vide conserve la photo actuelle.</span>
                @endif
            </div>

            @if ($realisation->image)
                <div style="margin: 8px 0 16px;">
                    <img src="{{ asset($realisation->image) }}" alt="Photo actuelle" style="max-height: 170px; border: 1px solid var(--border);">
                </div>
                <div class="field">
                    <label>
                        <input type="checkbox" name="remove_image" value="1">
                        Supprimer la photo
                    </label>
                </div>
            @endif
        </div>

        <div class="panel">
            <h2>Publication</h2>

            <div class="field">
                <label>
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $realisation->exists ? $realisation->is_published : true))>
                    Réalisation publiée (visible sur le site)
                </label>
            </div>

            <div class="field">
                <label for="sort_order">Ordre d'affichage <span class="hint">À date égale, les plus petits numéros apparaissent en premier.</span></label>
                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $realisation->sort_order ?? 0) }}" min="0">
            </div>
        </div>

        <div style="display: flex; gap: 12px;">
            <button type="submit" class="btn btn-green">{{ $realisation->exists ? 'Mettre à jour' : 'Créer la réalisation' }}</button>
            <a href="{{ route('admin.realisations.index') }}" class="btn btn-ghost">Annuler</a>
        </div>
    </form>

@endsection
