@extends('admin.layouts.app')

@section('title', 'Messages reçus')

@section('content')

    <div class="panel">
        <h2>Messages du formulaire de contact</h2>

        @if (session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Statut</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Objet</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($messages as $message)
                        <tr>
                            <td>
                                <span class="badge {{ $message->is_read ? '' : 'badge-green' }}">
                                    {{ $message->is_read ? 'Lu' : 'Non lu' }}
                                </span>
                            </td>
                            <td><strong>{{ $message->name }}</strong></td>
                            <td>{{ $message->email }}</td>
                            <td>{{ $message->subject }}</td>
                            <td style="max-width: 320px;">{{ \Illuminate\Support\Str::limit($message->message, 90) }}</td>
                            <td>{{ $message->created_at->format('d/m/Y H:i') }}</td>
                            <td style="white-space: nowrap;">
                                @unless ($message->is_read)
                                    <form method="POST" action="{{ route('admin.messages.read', $message) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-ghost btn-sm">Marquer lu</button>
                                    </form>
                                @endunless
                                <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" style="display: inline;" onsubmit="return confirm('Supprimer ce message ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">Aucun message reçu pour le moment.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $messages->links() }}
        </div>
    </div>

@endsection
