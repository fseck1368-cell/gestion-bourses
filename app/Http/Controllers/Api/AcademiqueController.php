<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Convention;
use App\Models\User;
use App\Services\AcademiqueService;
use Illuminate\Http\Request;

class AcademiqueController extends Controller
{
    public function evaluerRenouvellement(Convention $convention)
    {
        $result = app(AcademiqueService::class)->evaluerRenouvellement($convention);
        return response()->json($result);
    }

    public function progression(Request $request, ?User $user = null)
    {
        $etudiantId = $user ? $user->id : $request->user()->id;
        $result = app(AcademiqueService::class)->analyserProgression($etudiantId);
        return response()->json($result);
    }

    public function fiabilite(Request $request, ?User $user = null)
    {
        $etudiantId = $user ? $user->id : $request->user()->id;
        $result = app(AcademiqueService::class)->scoresFiabilite($etudiantId);
        return response()->json($result);
    }
}
