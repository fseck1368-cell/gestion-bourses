<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alerte;
use Illuminate\Http\Request;

class AlerteController extends Controller
{
    public function index(Request $request)
    {
        $alertes = Alerte::where('user_id', $request->user()->id)
            ->actives()
            ->latest()
            ->paginate(15);

        return response()->json($alertes);
    }

    public function nonLues(Request $request)
    {
        $count = Alerte::where('user_id', $request->user()->id)
            ->nonLues()
            ->actives()
            ->count();

        return response()->json(['count' => $count]);
    }

    public function marquerLue(Alerte $alerte)
    {
        $alerte->update(['lue' => true]);
        return response()->json($alerte);
    }

    public function marquerToutesLues(Request $request)
    {
        Alerte::where('user_id', $request->user()->id)
            ->where('lue', false)
            ->update(['lue' => true]);

        return response()->json(['message' => 'Toutes les alertes marquées comme lues.']);
    }
}
