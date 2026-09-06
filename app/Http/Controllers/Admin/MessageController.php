<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        $messages = Contact::query()
            ->latest()
            ->paginate(20);

        return view('admin.messages', ['messages' => $messages]);
    }

    public function markRead(Request $request, Contact $message): RedirectResponse
    {
        $message->update(['is_read' => true]);

        return back()->with('success', 'Message marqué comme lu.');
    }

    public function destroy(Contact $message): RedirectResponse
    {
        $message->delete();

        return back()->with('success', 'Message supprimé.');
    }
}
