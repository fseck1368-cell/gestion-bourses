<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alerte;
use App\Services\AlerteService;
use Illuminate\Http\Request;

class AlerteController extends Controller
{
    public function index(AlerteService $service)
    {
        $service->genererAlertes();
        $alertes = Alerte::actives()->latest()->paginate(20);
        return view('admin.alertes.index', compact('alertes'));
    }

    public function marquerLue(Alerte $alerte)
    {
        $alerte->update(['lue' => true]);
        return back()->with('success', 'Alerte marquée comme lue.');
    }

    public function marquerToutesLues()
    {
        Alerte::nonLues()->update(['lue' => true]);
        return back()->with('success', 'Toutes les alertes marquées comme lues.');
    }
}
