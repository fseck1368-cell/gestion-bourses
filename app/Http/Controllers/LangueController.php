<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LangueController extends Controller
{
    public function switch(string $locale)
    {
        if (!in_array($locale, ['fr', 'en'])) {
            abort(400);
        }

        session(['locale' => $locale]);

        return back()->with('success', $locale === 'en' ? 'Language changed' : 'Langue modifiée');
    }
}
