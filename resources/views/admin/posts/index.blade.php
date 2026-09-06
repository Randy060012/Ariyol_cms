@extends('admin.layouts.app')

@section('title', 'Articles du blog')

@section('content')

    <div class="panel">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 16px;">
            <h2 style="margin-bottom: 0;">Tous les articles</h2>
            <a href="{{ route('admin.posts.create') }}" class="btn btn-green">Écrire un article</a>
        </div>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Article</th>
                        <th>Catégorie</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($posts as $post)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    @if ($post->main_image)
                                        <img src="{{ asset($post->main_image) }}" alt="" style="width: 56px; height: 40px; object-fit: cover; border: 1px solid var(--border);">
                                    @endif
                                    <div>
                                        <strong>{{ $post->title }}</strong>
                                        <span style="display: block; font-size: 12px; color: var(--muted);">/blog/{{ $post->slug }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $post->category ?: '&mdash;' }}</td>
                            <td>{{ $post->created_at->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge {{ $post->is_published ? 'badge-green' : '' }}">
                                    {{ $post->is_published ? 'Publié' : 'Brouillon' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-ghost btn-sm">Modifier</a>
                                <a href="{{ route('blog.show', $post) }}" target="_blank" class="btn btn-ghost btn-sm">Voir</a>
                                <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" style="display: inline;" onsubmit="return confirm('Supprimer cet article ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Aucun article. Écrivez le premier.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
