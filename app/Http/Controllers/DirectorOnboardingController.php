<?php

namespace App\Http\Controllers;

use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DirectorOnboardingController extends Controller
{
    public function showRegisterUniversity()
    {
        $user = Auth::user();

        if ($user->university_id) {
            return redirect()->route('director.pending');
        }

        return view('director.register-university');
    }

    public function storeUniversity(Request $request)
    {
        $data = $request->validate([
            'nom'       => ['required', 'string', 'max:255'],
            'acronyme'  => ['nullable', 'string', 'max:20'],
            'email'     => ['required', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'site_web'  => ['nullable', 'url', 'max:255'],
        ]);

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
