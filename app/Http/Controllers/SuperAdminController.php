<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\University;
use App\Models\UniversityReference;
use App\Models\ReponseQuestionnaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        // KPIs
        $nbUniversites  = University::where('statut', 'active')->count();
        $nbUtilisateurs = User::whereNot('role', 'superadmin')->count();
        $nbEvaluations  = ReponseQuestionnaire::count();
        $enAttente      = University::where('statut', 'en_attente')->count();

        // Demandes récentes
        $demandesRecentes = University::with('directeur')
            ->where('statut', 'en_attente')
            ->latest()
            ->take(5)
            ->get();

        // Évaluations par mois (6 derniers mois)
        $mois6 = collect();
        for ($i = 5; $i >= 0; $i--) {
            $d = now()->subMonths($i);
            $mois6->push([
                'label' => ucfirst($d->locale('fr')->isoFormat('MMMM')),
                'count' => ReponseQuestionnaire::whereYear('soumis_at', $d->year)
                               ->whereMonth('soumis_at', $d->month)->count(),
            ]);
        }

        // Évaluations par mois (12 derniers mois)
        $mois12 = collect();
        for ($i = 11; $i >= 0; $i--) {
            $d = now()->subMonths($i);
            $mois12->push([
                'label' => ucfirst($d->locale('fr')->isoFormat('MMM')),
                'count' => ReponseQuestionnaire::whereYear('soumis_at', $d->year)
                               ->whereMonth('soumis_at', $d->month)->count(),
            ]);
        }

        // Top 5 universités par évaluations
        $topUniversites = University::where('statut', 'active')
            ->get()
            ->map(fn($u) => [
                'nom'   => $u->acronyme ?? $u->nom,
                'count' => ReponseQuestionnaire::whereHas('lien.annexe',
                    fn($q) => $q->where('university_id', $u->id))->count(),
            ])
            ->sortByDesc('count')
            ->take(5)
            ->values();

        // Activité récente : dernières inscriptions traitées
        $activiteRecente = University::with(['directeur', 'validateur'])
            ->whereIn('statut', ['active', 'rejetee'])
            ->latest('validee_at')
            ->take(5)
            ->get();

        return view('SuperAdmin.dashboard', compact(
            'nbUniversites', 'nbUtilisateurs', 'nbEvaluations', 'enAttente',
            'demandesRecentes', 'mois6', 'mois12', 'topUniversites', 'activiteRecente'
        ));
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

        AuditLog::write('inscription_approuvee', "Université « {$university->nom} » approuvée.", 'University', $university->id);

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

        AuditLog::write('inscription_rejetee', "Université « {$university->nom} » rejetée. Motif : {$request->motif}", 'University', $university->id, 'warning');

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

    public function logs(Request $request)
    {
        $query = \App\Models\AuditLog::with('user')->latest();

        if ($search = $request->input('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('prenom', 'like', "%$search%")
                ->orWhere('nom', 'like', "%$search%")
            );
        }
        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }
        if ($niveau = $request->input('niveau')) {
            $query->where('niveau', $niveau);
        }
        if ($debut = $request->input('date_debut')) {
            $query->whereDate('created_at', '>=', $debut);
        }
        if ($fin = $request->input('date_fin')) {
            $query->whereDate('created_at', '<=', $fin);
        }

        $logs   = $query->paginate(20)->withQueryString();
        $total  = \App\Models\AuditLog::count();
        $actions = \App\Models\AuditLog::distinct()->pluck('action')->sort()->values();

        return view('SuperAdmin.logs', compact('logs', 'total', 'actions'));
    }
}
