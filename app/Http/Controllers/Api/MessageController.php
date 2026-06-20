<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dossier;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Dossier $dossier)
    {
        $messages = $dossier->messages()->with('user')->get();
        return response()->json($messages);
    }

    public function store(Request $request, Dossier $dossier)
    {
        $request->validate([
            'contenu' => 'required|string',
            'demande_complement' => 'boolean',
        ]);

        $message = Message::create([
            'dossier_id' => $dossier->id,
            'user_id' => $request->user()->id,
            'contenu' => $request->contenu,
            'demande_complement' => $request->demande_complement ?? false,
            'lu' => false,
        ]);

        return response()->json($message->load('user'), 201);
    }

    public function markRead(Request $request, Dossier $dossier)
    {
        $dossier->messages()
            ->where('user_id', '!=', $request->user()->id)
            ->where('lu', false)
            ->update(['lu' => true]);

        return response()->json(['message' => 'Messages marqués comme lus.']);
    }
}
