@extends('admin.layouts.app')

@section('title', 'Tableau de bord')

@section('content')

    <div class="stat-cards">
        <div class="stat-card">
            <strong>{{ $pagesCount }}</strong>
            <span>Pages gérées</span>
        </div>
        
        <div class="stat-card green-top">
            <strong>{{ $sectionsCount }}</strong>
            <span>Sections de contenu</span>
        </div>
        <div class="stat-card">
            <strong>{{ $postsCount }}</strong>
            <span>Articles du blog</span>
        </div>
        <div class="stat-card">
            <strong>{{ $settingsCount }}</strong>
            <span>Paramètres du site</span>
        </div>
        <div class="stat-card green-top">
            <strong>{{ $unreadMessages }}</strong>
            <span>Messages non lus ({{ $totalMessages }} au total)</span>
        </div>
    </div>

    <div class="panel">
        <h2>Actions rapides</h2>
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            <a href="{{ route('admin.pages.index') }}" class="btn btn-primary">Gérer les pages</a>
            <a href="{{ route('admin.posts.create') }}" class="btn btn-green">Écrire un article</a>
            <a href="{{ route('admin.settings.edit') }}" class="btn btn-ghost">Modifier header / footer</a>
            <a href="{{ route('admin.messages.index') }}" class="btn btn-ghost">Voir les messages</a>
            <a href="{{ route('home') }}" target="_blank" class="btn btn-ghost">Voir le site</a>
        </div>
    </div>

    <div class="panel">
        <h2>Derniers messages reçus</h2>

        @if ($recentMessages->isEmpty())
            <p class="panel-sub">Aucun message pour le moment.</p>
        @else
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Objet</th>
                            <th>Date</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentMessages as $message)
                            <tr>
                                <td>{{ $message->name }}</td>
                                <td>{{ $message->subject }}</td>
                                <td>{{ $message->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge {{ $message->is_read ? '' : 'badge-green' }}">
                                        {{ $message->is_read ? 'Lu' : 'Non lu' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection
