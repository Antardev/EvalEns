<?php

namespace App\Http\Controllers;

use App\Models\University;
use App\Models\UniversityReference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DirectorOnboardingController extends Controller
{
    public function showRegisterUniversity()
    {
        $user = Auth::user();

        // Déjà une université soumise → page d'attente
        if ($user->university_id) {
            return redirect()->route('director.pending');
        }

        $universities = UniversityReference::orderBy('nom')->get(['nom', 'acronyme']);

        return view('director.register-university', compact('universities'));
    }

    public function storeUniversity(Request $request)
    {
        $validNoms = UniversityReference::pluck('nom')->all();

        $data = $request->validate([
            'nom'       => ['required', 'string', 'max:255', Rule::in($validNoms)],
            'email'     => ['required', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'site_web'  => ['nullable', 'url', 'max:255'],
        ]);

        // L'acronyme vient toujours de la DB, jamais du client
        $ref = UniversityReference::where('nom', $data['nom'])->first();
        $data['acronyme'] = $ref?->acronyme;

        $user = Auth::user();

        $university = University::create(array_merge($data, [
            'statut'       => 'en_attente',
            'directeur_id' => $user->id,
        ]));

        $user->update(['university_id' => $university->id]);

        return redirect()->route('director.pending');
    }

    public function pending()
    {
        $user       = Auth::user();
        $university = $user->university;

        // Pas encore d'université soumise
        if (! $university) {
            return redirect()->route('director.register-university');
        }

        // Université validée → dashboard
        if ($university->isActive()) {
            return redirect()->route('adminuniversity.dashboard');
        }

        return view('director.pending', compact('university'));
    }
}
