<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parametre;
use Illuminate\Http\Request;

class ParametreController extends Controller
{
    public function index()
    {
        $parametres = Parametre::all()->groupBy('groupe');
        return response()->json($parametres);
    }

    public function update(Request $request)
    {
        $request->validate([
            'parametres' => 'required|array',
            'parametres.*.cle' => 'required|string',
            'parametres.*.valeur' => 'required|string',
        ]);

        foreach ($request->parametres as $param) {
            Parametre::set($param['cle'], $param['valeur']);
        }

        return response()->json(['message' => 'Paramètres mis à jour.']);
    }
}
