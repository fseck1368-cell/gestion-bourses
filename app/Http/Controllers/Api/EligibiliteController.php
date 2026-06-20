<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EligibiliteService;
use Illuminate\Http\Request;

class EligibiliteController extends Controller
{
    public function verifier(Request $request)
    {
        $eligibilite = app(EligibiliteService::class)->verifierEligibilite(
            $request->user(),
            $request->campagne_id
        );

        return response()->json($eligibilite);
    }

    public function quotaFiliere(Request $request)
    {
        $request->validate(['filiere' => 'required|string']);

        $quota = app(EligibiliteService::class)->verifierQuotaFiliere(
            $request->filiere,
            $request->campagne_id
        );

        return response()->json($quota);
    }
}
