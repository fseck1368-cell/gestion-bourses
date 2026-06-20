<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FinanceService;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function plafondCumul(Request $request, User $user)
    {
        $result = app(FinanceService::class)->verifierPlafondCumul($user->id);
        return response()->json($result);
    }

    public function anomalies(Request $request, User $user)
    {
        $anomalies = app(FinanceService::class)->detecterAnomalies($user->id);
        return response()->json(['anomalies' => $anomalies]);
    }

    public function verifierMontant(Request $request)
    {
        $request->validate(['montant' => 'required|numeric|min:0']);

        $doubleValidation = app(FinanceService::class)->necessiteDoubleValidation($request->montant);

        return response()->json([
            'montant' => $request->montant,
            'double_validation_requise' => $doubleValidation,
        ]);
    }
}
