<?php

namespace App\Http\Controllers\Etudiant;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Dossier;
use App\Models\Historique;
use App\Services\EligibiliteService;
use App\Services\ScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DossierController extends Controller
{
    public function create()
    {
        $eligibilite = app(EligibiliteService::class)->verifierEligibilite(auth()->user());

        return view('etudiant.dossiers.create', compact('eligibilite'));
    }

    public function store(Request $request)
    {
        $eligibiliteService = app(EligibiliteService::class);

        // Vérification de doublon
        $doublon = $eligibiliteService->verifierDoublon(auth()->id(), $request->annee_universitaire);
        if ($doublon) {
            return back()->withInput()->with('error', 'Vous avez déjà un dossier pour cette année universitaire (Réf: ' . $doublon->reference . ').');
        }

        $validated = $request->validate([
            'annee_universitaire' => 'required|string|max:20',
            'niveau_etude' => 'required|string|max:50',
            'filiere' => 'required|string|max:100',
            'etablissement' => 'required|string|max:200',
            'moyenne_generale' => 'nullable|numeric|min:0|max:20',
            'situation_sociale' => 'nullable|string|max:2000',
            'revenu_familial' => 'nullable|numeric|min:0',
            'nombre_freres_soeurs' => 'nullable|integer|min:0',
            'motif_demande' => 'required|string|max:3000',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'types_documents.*' => 'nullable|string',
        ]);

        $dossier = Dossier::create([
            'reference' => Dossier::genererReference(),
            'etudiant_id' => auth()->id(),
            'statut' => 'soumis',
            'annee_universitaire' => $validated['annee_universitaire'],
            'niveau_etude' => $validated['niveau_etude'],
            'filiere' => $validated['filiere'],
            'etablissement' => $validated['etablissement'],
            'moyenne_generale' => $validated['moyenne_generale'] ?? null,
            'situation_sociale' => $validated['situation_sociale'] ?? null,
            'revenu_familial' => $validated['revenu_familial'] ?? null,
            'nombre_freres_soeurs' => $validated['nombre_freres_soeurs'] ?? null,
            'motif_demande' => $validated['motif_demande'],
            'date_soumission' => now(),
        ]);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $index => $file) {
                $path = $file->store('dossiers/' . $dossier->id, 'public');
                Document::create([
                    'dossier_id' => $dossier->id,
                    'nom_fichier' => $file->getClientOriginalName(),
                    'chemin' => $path,
                    'type_document' => $request->input('types_documents.' . $index, 'autre'),
                    'mime_type' => $file->getMimeType(),
                    'taille' => $file->getSize(),
                ]);
            }
        }

        Historique::create([
            'dossier_id' => $dossier->id,
            'user_id' => auth()->id(),
            'action' => 'Soumission du dossier',
            'nouveau_statut' => 'soumis',
        ]);

        // Calcul automatique du score d'éligibilité
        app(ScoringService::class)->calculerScore($dossier);

        return redirect()->route('etudiant.dossiers.show', $dossier)
            ->with('success', 'Votre dossier a été soumis avec succès. Référence : ' . $dossier->reference);
    }

    public function show(Dossier $dossier)
    {
        if ($dossier->etudiant_id !== auth()->id()) {
            abort(403);
        }

        $dossier->load(['documents', 'historiques.user', 'instructeur']);
        return view('etudiant.dossiers.show', compact('dossier'));
    }

    public function edit(Dossier $dossier)
    {
        if ($dossier->etudiant_id !== auth()->id()) {
            abort(403);
        }

        if (!$dossier->estModifiable()) {
            return back()->with('error', 'Ce dossier ne peut plus être modifié.');
        }

        $dossier->load('documents');
        return view('etudiant.dossiers.edit', compact('dossier'));
    }

    public function update(Request $request, Dossier $dossier)
    {
        if ($dossier->etudiant_id !== auth()->id() || !$dossier->estModifiable()) {
            abort(403);
        }

        $validated = $request->validate([
            'annee_universitaire' => 'required|string|max:20',
            'niveau_etude' => 'required|string|max:50',
            'filiere' => 'required|string|max:100',
            'etablissement' => 'required|string|max:200',
            'moyenne_generale' => 'nullable|numeric|min:0|max:20',
            'situation_sociale' => 'nullable|string|max:2000',
            'revenu_familial' => 'nullable|numeric|min:0',
            'nombre_freres_soeurs' => 'nullable|integer|min:0',
            'motif_demande' => 'required|string|max:3000',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'types_documents.*' => 'nullable|string',
        ]);

        $dossier->update($validated);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $index => $file) {
                $path = $file->store('dossiers/' . $dossier->id, 'public');
                Document::create([
                    'dossier_id' => $dossier->id,
                    'nom_fichier' => $file->getClientOriginalName(),
                    'chemin' => $path,
                    'type_document' => $request->input('types_documents.' . $index, 'autre'),
                    'mime_type' => $file->getMimeType(),
                    'taille' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('etudiant.dossiers.show', $dossier)
            ->with('success', 'Dossier mis à jour avec succès.');
    }

    public function destroyDocument(Document $document)
    {
        $dossier = $document->dossier;

        if ($dossier->etudiant_id !== auth()->id() || !$dossier->estModifiable()) {
            abort(403);
        }

        Storage::disk('public')->delete($document->chemin);
        $document->delete();

        return back()->with('success', 'Document supprimé.');
    }
}
