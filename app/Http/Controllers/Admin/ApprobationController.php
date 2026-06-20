<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Approbation;
use App\Models\Dossier;
use App\Models\User;
use Illuminate\Http\Request;

class ApprobationController extends Controller
{
    public function index()
    {
        $approbations = Approbation::with(['dossier.etudiant', 'approbateur'])
            ->where('approbateur_id', auth()->id())
            ->where('statut', 'en_attente')
            ->latest()->paginate(15);

        return view('admin.approbations.index', compact('approbations'));
    }

    public function configurer(Dossier $dossier)
    {
        $admins = User::where('role', 'administrateur')->where('actif', true)->get();
        $approbations = Approbation::where('dossier_id', $dossier->id)->with('approbateur')->orderBy('ordre')->get();

        return view('admin.approbations.configurer', compact('dossier', 'admins', 'approbations'));
    }

    public function store(Request $request, Dossier $dossier)
    {
        $validated = $request->validate([
            'approbateurs' => 'required|array|min:1',
            'approbateurs.*' => 'exists:users,id',
        ]);

        Approbation::where('dossier_id', $dossier->id)->delete();

        foreach ($validated['approbateurs'] as $ordre => $userId) {
            Approbation::create([
                'dossier_id' => $dossier->id,
                'approbateur_id' => $userId,
                'ordre' => $ordre + 1,
            ]);
        }

        return redirect()->route('admin.dossiers.show', $dossier)
            ->with('success', 'Workflow d\'approbation configuré.');
    }

    public function approuver(Request $request, Approbation $approbation)
    {
        if ($approbation->approbateur_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $approbation->update([
            'statut' => 'approuve',
            'commentaire' => $validated['commentaire'],
            'date_decision' => now(),
        ]);

        $prochaine = Approbation::where('dossier_id', $approbation->dossier_id)
            ->where('ordre', $approbation->ordre + 1)
            ->first();

        if (!$prochaine) {
            $approbation->dossier->update([
                'statut' => 'accepte',
                'date_decision' => now(),
                'commentaire_admin' => 'Approuvé par workflow multi-niveaux.',
            ]);
        }

        return back()->with('success', 'Dossier approuvé.');
    }

    public function rejeter(Request $request, Approbation $approbation)
    {
        if ($approbation->approbateur_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'commentaire' => 'required|string|max:1000',
        ]);

        $approbation->update([
            'statut' => 'rejete',
            'commentaire' => $validated['commentaire'],
            'date_decision' => now(),
        ]);

        $approbation->dossier->update([
            'statut' => 'rejete',
            'date_decision' => now(),
            'commentaire_admin' => 'Rejeté au niveau ' . $approbation->ordre . ': ' . $validated['commentaire'],
        ]);

        return back()->with('success', 'Dossier rejeté.');
    }
}
