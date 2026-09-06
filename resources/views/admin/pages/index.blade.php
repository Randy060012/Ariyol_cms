@extends('admin.layouts.app')

@section('title', 'Pages')

@section('content')

    <div class="panel">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 16px;">
            <h2 style="margin-bottom: 0;">Toutes les pages</h2>
            <a href="{{ route('admin.pages.create') }}" class="btn btn-green">Créer une page</a>
        </div>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>URL</th>
                        <th>Sections</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pages as $page)
                        <tr>
                            <td>
                                <strong>{{ $page->title }}</strong>
                                @if (in_array($page->page_key, ['home', 'about', 'programmes', 'realisations', 'contact']))
                                    <span class="badge">Page essentielle</span>
                                @endif
                            </td>
                            <td>/{{ $page->slug }}</td>
                            <td>{{ $page->sections_count }}</td>
                            <td>
                                <span class="badge {{ $page->is_published ? 'badge-green' : '' }}">
                                    {{ $page->is_published ? 'Publiée' : 'Brouillon' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-ghost btn-sm">Modifier</a>
                                <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" style="display: inline;" onsubmit="return confirm('Supprimer cette page ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Aucune page. Créez la première.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
