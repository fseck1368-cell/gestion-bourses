<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    private function authorizeAccess(Dossier $dossier): void
    {
        $user = Auth::user();
        if ($user->id !== $dossier->etudiant_id && $user->id !== $dossier->instructeur_id) {
            abort(403);
        }
    }

    public function show(Dossier $dossier)
    {
        $this->authorizeAccess($dossier);
        $dossier->load(['etudiant', 'instructeur']);

        $otherUser = Auth::id() === $dossier->etudiant_id ? $dossier->instructeur : $dossier->etudiant;

        return view('chat.show', [
            'dossier' => $dossier,
            'otherUser' => $otherUser,
            'currentUser' => Auth::user(),
        ]);
    }

    public function messages(Dossier $dossier)
    {
        $this->authorizeAccess($dossier);

        $messages = Message::where('dossier_id', $dossier->id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'user_id' => $msg->user_id,
                    'contenu' => $msg->contenu,
                    'user_name' => $msg->user?->name ?? 'Utilisateur',
                    'lu' => $msg->lu,
                    'timestamp' => $msg->created_at->isToday() ? $msg->created_at->format('H:i') : $msg->created_at->format('d/m H:i'),
                ];
            });

        $unreadCount = Message::where('dossier_id', $dossier->id)
            ->where('user_id', '!=', Auth::id())
            ->where('lu', false)->count();

        return response()->json(['messages' => $messages, 'unread_count' => $unreadCount]);
    }

    public function send(Request $request, Dossier $dossier)
    {
        $this->authorizeAccess($dossier);

        $validated = $request->validate([
            'contenu' => 'required|string|max:2000',
        ]);

        $message = Message::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'contenu' => $validated['contenu'],
            'demande_complement' => false,
            'lu' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'user_id' => $message->user_id,
                'contenu' => $message->contenu,
                'user_name' => Auth::user()->name,
                'lu' => false,
                'timestamp' => $message->created_at->format('H:i'),
            ],
        ]);
    }

    public function markRead(Dossier $dossier)
    {
        $this->authorizeAccess($dossier);

        Message::where('dossier_id', $dossier->id)
            ->where('user_id', '!=', Auth::id())
            ->where('lu', false)
            ->update(['lu' => true]);

        return response()->json(['success' => true]);
    }
}
