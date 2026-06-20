<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Dossier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Dossier $dossier)
    {
        return response()->json($dossier->documents);
    }

    public function store(Request $request, Dossier $dossier)
    {
        $request->validate([
            'fichier' => 'required|file|max:10240',
            'type_document' => 'required|string',
        ]);

        $file = $request->file('fichier');
        $path = $file->store('documents/' . $dossier->id, 'public');

        $document = Document::create([
            'dossier_id' => $dossier->id,
            'nom_fichier' => $file->getClientOriginalName(),
            'chemin' => $path,
            'type_document' => $request->type_document,
            'mime_type' => $file->getMimeType(),
            'taille' => $file->getSize(),
        ]);

        return response()->json($document, 201);
    }

    public function destroy(Request $request, Document $document)
    {
        $user = $request->user();

        if ($user->isEtudiant() && $document->dossier->etudiant_id !== $user->id) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        if (!$document->dossier->estModifiable() && $user->isEtudiant()) {
            return response()->json(['message' => 'Le dossier ne peut plus être modifié.'], 422);
        }

        Storage::disk('public')->delete($document->chemin);
        $document->delete();

        return response()->json(['message' => 'Document supprimé.']);
    }
}
