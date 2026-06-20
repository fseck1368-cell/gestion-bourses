<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request, Dossier $dossier)
    {
        $validated = $request->validate([
            'contenu' => 'required|string|max:2000',
            'demande_complement' => 'boolean',
        ]);

        Message::create([
            'dossier_id' => $dossier->id,
            'user_id' => auth()->id(),
            'contenu' => $validated['contenu'],
            'demande_complement' => $request->boolean('demande_complement'),
        ]);

        return back()->with('success', 'Message envoyé.');
    }

    public function marquerLu(Message $message)
    {
        $message->update(['lu' => true]);
        return back();
    }
}
