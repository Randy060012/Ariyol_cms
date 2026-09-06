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
