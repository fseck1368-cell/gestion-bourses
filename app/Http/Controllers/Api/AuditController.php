<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = \DB::table('audit_logs')
            ->join('users', 'audit_logs.user_id', '=', 'users.id')
            ->select('audit_logs.*', 'users.nom', 'users.prenom', 'users.email')
            ->orderBy('audit_logs.created_at', 'desc');

        if ($request->has('user_id')) {
            $query->where('audit_logs.user_id', $request->user_id);
        }

        if ($request->has('action')) {
            $query->where('audit_logs.action', $request->action);
        }

        if ($request->has('entite')) {
            $query->where('audit_logs.entite', $request->entite);
        }

        return response()->json($query->paginate(30));
    }

    public function dossier(int $dossierId)
    {
        $logs = app(AuditService::class)->historiqueAcces('dossier', $dossierId);
        return response()->json($logs);
    }

    public function utilisateur(int $userId)
    {
        $logs = app(AuditService::class)->activiteUtilisateur($userId);
        return response()->json($logs);
    }
}
