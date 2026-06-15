<?php

namespace App\Http\Controllers;

use App\Models\University;
use App\Models\UniversityReference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $enAttente = University::where('statut', 'en_attente')->count();
        return view('SuperAdmin.dashboard', compact('enAttente'));
    }

    public function inscriptions()
    {
        $enAttente = University::with('directeur')
            ->where('statut', 'en_attente')
            ->latest()
            ->get();

        return view('SuperAdmin.inscriptions', [
            'activeTab' => 'attente',
            'enAttente' => $enAttente,
        ]);
    }

    public function inscriptionsHistorique()
    {
        $historique = University::with(['directeur', 'validateur'])
            ->whereIn('statut', ['active', 'rejetee'])
            ->latest('validee_at')
            ->get();

        return view('SuperAdmin.inscriptions', [
            'activeTab' => 'historique',
            'historique' => $historique,
        ]);
    }

    public function approuverInscription(Request $request, $id)
    {
        $university = University::findOrFail($id);
        $university->update([
            'statut'     => 'active',
            'validee_at' => now(),
            'validee_par' => Auth::id(),
        ]);

        // Lier le directeur à son université
        $university->directeur->update(['university_id' => $university->id]);

        return redirect()->route('superadmin.inscriptions')
            ->with('success', "L'université « {$university->nom} » a été approuvée.");
    }

    public function rejeterInscription(Request $request, $id)
    {
        $request->validate(['motif' => 'required|string|max:500']);

        $university = University::findOrFail($id);
        $university->update([
            'statut'      => 'rejetee',
            'motif_rejet' => $request->motif,
            'validee_at'  => now(),
            'validee_par' => Auth::id(),
        ]);

        return redirect()->route('superadmin.inscriptions')
            ->with('success', "La demande de « {$university->nom} » a été rejetée.");
    }

    public function utilisateurs(Request $request)
    {
        $query = \App\Models\User::with('university')
            ->whereNot('role', 'superadmin')
            ->latest();

        if ($search = $request->input('search')) {
            $query->where(fn($q) => $q->where('name', 'like', "%$search%")
                                      ->orWhere('email', 'like', "%$search%"));
        }
        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }
        if ($univId = $request->input('university_id')) {
            $query->where('university_id', $univId);
        }

        $users        = $query->paginate(20)->withQueryString();
        $universities = \App\Models\University::where('statut', 'active')->orderBy('nom')->get(['id', 'nom']);

        $counts = [
            'total'      => \App\Models\User::whereNot('role', 'superadmin')->count(),
            'etudiants'  => \App\Models\User::where('role', 'etudiant')->count(),
            'enseignants'=> \App\Models\User::where('role', 'enseignant')->count(),
            'directeurs' => \App\Models\User::where('role', 'directeur')->count(),
        ];

        return view('SuperAdmin.utilisateurs', compact('users', 'universities', 'counts'));
    }

    public function universites()
    {
        $references = UniversityReference::orderBy('nom')->get();

        return view('SuperAdmin.universites', compact('references'));
    }

    public function creerUniversite(Request $request)
    {
        $request->validate([
            'nom'      => ['required', 'string', 'max:255', 'unique:university_references,nom'],
            'acronyme' => ['nullable', 'string', 'max:20'],
        ]);

        UniversityReference::create([
            'nom'      => $request->nom,
            'acronyme' => $request->acronyme ?: null,
        ]);

        return redirect()->route('superadmin.universites')
            ->with('success', "L'université « {$request->nom} » a été ajoutée au référentiel.");
    }

    public function modifierUniversite(Request $request, $id)
    {
        $reference = UniversityReference::findOrFail($id);

        $request->validate([
            'nom'      => ['required', 'string', 'max:255', Rule::unique('university_references', 'nom')->ignore($id)],
            'acronyme' => ['nullable', 'string', 'max:20'],
        ]);

        $reference->update([
            'nom'      => $request->nom,
            'acronyme' => $request->acronyme ?: null,
        ]);

        return redirect()->route('superadmin.universites')
            ->with('success', "L'université « {$reference->nom} » a été mise à jour.");
    }

    public function supprimerUniversite($id)
    {
        $reference = UniversityReference::findOrFail($id);
        $nom = $reference->nom;
        $reference->delete();

        return redirect()->route('superadmin.universites')
            ->with('success', "L'université « {$nom} » a été supprimée du référentiel.");
    }

    public function criteres()
    {
        return view('SuperAdmin.criteres');
    }

    public function saveCriteres(Request $request)
    {
        $request->validate([
            'criteres'           => 'required|array',
            'criteres.*.nom'     => 'required|string|max:255',
            'criteres.*.poids'   => 'required|numeric|min:0|max:100',
        ]);
        // TODO: save criteria logic
        return redirect()->route('superadmin.criteres')
            ->with('success', 'Critères enregistrés avec succès.');
    }

    public function statistiques()
    {
        return view('SuperAdmin.statistiques');
    }

    public function rapports()
    {
        return view('SuperAdmin.rapports');
    }

    public function exporterRapport(Request $request)
    {
        $request->validate([
            'type'       => 'required|in:global,universite,enseignant',
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after_or_equal:date_debut',
        ]);
        // TODO: generate PDF using DomPDF
        return redirect()->route('superadmin.rapports')
            ->with('success', 'Rapport généré avec succès.');
    }

    public function logs()
    {
        return view('SuperAdmin.logs');
    }
}
