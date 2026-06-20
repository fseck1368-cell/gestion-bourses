<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parametre;
use Illuminate\Http\Request;

class ParametreController extends Controller
{
    public function index()
    {
        $parametres = Parametre::orderBy('groupe')->orderBy('label')->get()->groupBy('groupe');
        return view('admin.parametres.index', compact('parametres'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'parametres' => 'required|array',
            'parametres.*' => 'nullable|string|max:1000',
        ]);

        foreach ($validated['parametres'] as $cle => $valeur) {
            Parametre::where('cle', $cle)->update(['valeur' => $valeur]);
        }

        return back()->with('success', 'Paramètres mis à jour avec succès.');
    }
}
