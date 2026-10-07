@extends('admin.layouts.app')

@section('title', 'Réalisations')

@section('content')

    <div class="panel">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 16px;">
            <h2 style="margin-bottom: 0;">Toutes les réalisations</h2>
            <a href="{{ route('admin.realisations.create') }}" class="btn btn-green">Ajouter une réalisation</a>
        </div>

        <p class="panel-sub" style="margin-top: -6px;">Chaque réalisation publiée possède sa page de détail, avec récit, photos, date et lieu. Les plus récentes sont aussi mises en avant sur l’accueil.</p>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Réalisation</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($realisations as $realisation)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    @if ($realisation->image)
                                        <img src="{{ asset($realisation->image) }}" alt="" style="width: 56px; height: 40px; object-fit: cover; border: 1px solid var(--border);">
                                    @else
                                        <span style="width: 56px; height: 40px; display: inline-flex; align-items: center; justify-content: center; background: var(--paper); border: 1px dashed var(--border); font-size: 10px; color: var(--muted);">—</span>
                                    @endif
                                    <div>
                                        <strong>{{ $realisation->title }}</strong>
                                        @if ($realisation->location)
                                            <span style="display: block; font-size: 12px; color: var(--muted);">{{ $realisation->location }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $realisation->date?->format('d/m/Y') ?? '&mdash;' }}</td>
                            <td>
                                <span class="badge {{ $realisation->is_published ? 'badge-green' : '' }}">
                                    {{ $realisation->is_published ? 'Publiée' : 'Brouillon' }}
                                </span>
                            </td>
                            <td>
                                @if ($realisation->is_published && $realisation->slug)
                                    <a href="{{ route('realisations.show', $realisation->slug) }}" target="_blank" rel="noopener" class="btn btn-ghost btn-sm">Voir</a>
                                @endif
                                <a href="{{ route('admin.realisations.edit', $realisation) }}" class="btn btn-ghost btn-sm">Modifier</a>
                                <form method="POST" action="{{ route('admin.realisations.destroy', $realisation) }}" style="display: inline;" onsubmit="return confirm('Supprimer cette réalisation ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">Aucune réalisation. Ajoutez la première.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
