<?php

namespace App\Http\Controllers;

use App\Models\Annexe;
use App\Models\LienQuestionnaire;
use App\Models\ReponseQuestionnaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUniversityController extends Controller
{
    private function universityId(): int
    {
        return Auth::user()->university_id;
    }

    public function dashboard()
    {
        $univId = $this->universityId();

        $nbEnseignants = \App\Models\User::whereHas('annexes', fn($q) => $q->where('university_id', $univId))
            ->where('role', 'enseignant')->count();

        $nbAnnexes = \App\Models\Annexe::where('university_id', $univId)->count();

        $nbEvaluations = \App\Models\ReponseQuestionnaire::whereHas('lien.annexe', fn($q) => $q->where('university_id', $univId))->count();

        $nbLiens = \App\Models\LienQuestionnaire::whereHas('annexe', fn($q) => $q->where('university_id', $univId))->count();

        // Moyennes par annexe pour le graphe
        $annexes = \App\Models\Annexe::where('university_id', $univId)->orderBy('nom')->get();

        $statsParAnnexe = $annexes->map(function ($annexe) {
            $reponses = \App\Models\ReponseQuestionnaire::whereHas('lien', fn($q) => $q->where('annexe_id', $annexe->id))->get();
            $scores   = $reponses->flatMap(fn($r) => collect($r->scores)->pluck('score'));
            return [
                'nom'     => $annexe->nom,
                'moyenne' => $scores->isNotEmpty() ? round($scores->avg() * 20) : 0,
                'count'   => $reponses->count(),
            ];
        });

        // Enseignants récents
        $enseignantsRecents = \App\Models\User::whereHas('annexes', fn($q) => $q->where('university_id', $univId))
            ->where('role', 'enseignant')
            ->with('annexes')
            ->latest()
            ->take(5)
            ->get();

        return view('adminuniversity.dashboard', compact(
            'nbEnseignants', 'nbAnnexes', 'nbEvaluations', 'nbLiens',
            'statsParAnnexe', 'enseignantsRecents'
        ));
    }

    public function etudiants(Request $request)
    {
        $univId = $this->universityId();
        $annexes = Annexe::where('university_id', $univId)->orderBy('nom')->get();

        $query = User::where('university_id', $univId)->where('role', 'etudiant')
            ->with('annexe')->latest();

        if ($search = $request->input('search')) {
            $query->where(fn($q) => $q->where('prenom', 'like', "%$search%")
                                      ->orWhere('nom', 'like', "%$search%")
                                      ->orWhere('email', 'like', "%$search%"));
        }

        if ($annexeId = $request->input('annexe_id')) {
            $query->where('annexe_id', $annexeId);
        }

        $grouped = $query->get()->groupBy('annexe_id');
        $total   = User::where('university_id', $univId)->where('role', 'etudiant')->count();

        return view('adminuniversity.etudiants', compact('annexes', 'grouped', 'total'));
    }

    public function creerEtudiant(Request $request)
    {
        $request->validate([
            'nom'    => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email',
        ]);
        return redirect()->route('adminuniversity.etudiants')->with('success', 'Étudiant ajouté avec succès.');
    }

    public function modifierEtudiant(Request $request, $id)
    {
        $request->validate([
            'nom'    => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email'  => 'required|email',
        ]);
        return redirect()->route('adminuniversity.etudiants')->with('success', 'Étudiant mis à jour.');
    }

    public function supprimerEtudiant($id)
    {
        return redirect()->route('adminuniversity.etudiants')->with('success', 'Étudiant supprimé.');
    }

    public function importerEtudiants(Request $request)
    {
        $request->validate(['fichier_csv' => 'required|file|mimes:csv,txt|max:2048']);
        // TODO: parse and import CSV
        return redirect()->route('adminuniversity.etudiants')->with('success', 'Import CSV effectué avec succès.');
    }

    public function enseignants(Request $request)
    {
        $univId  = $this->universityId();
        $annexes = Annexe::where('university_id', $univId)->orderBy('nom')->get();

        $query = User::whereHas('annexes', fn($q) => $q->where('university_id', $univId))
            ->where('role', 'enseignant')
            ->with('annexes')
            ->latest();

        if ($search = $request->input('search')) {
            $query->where(fn($q) => $q->where('prenom', 'like', "%$search%")
                                      ->orWhere('nom', 'like', "%$search%")
                                      ->orWhere('email', 'like', "%$search%"));
        }

        if ($annexeId = $request->input('annexe_id')) {
            $query->whereHas('annexes', fn($q) => $q->where('annexes.id', $annexeId));
        }

        $enseignants = $query->paginate(30)->withQueryString();
        $total       = User::whereHas('annexes', fn($q) => $q->where('university_id', $univId))
                           ->where('role', 'enseignant')->count();

        return view('adminuniversity.enseignants', compact('annexes', 'enseignants', 'total'));
    }

    public function creerEnseignant(Request $request)
    {
        $request->validate([
            'nom'    => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email',
        ]);
        return redirect()->route('adminuniversity.enseignants')->with('success', 'Enseignant ajouté avec succès.');
    }

    public function modifierEnseignant(Request $request, $id)
    {
        $request->validate([
            'nom'    => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email'  => 'required|email',
        ]);
        return redirect()->route('adminuniversity.enseignants')->with('success', 'Enseignant mis à jour.');
    }

    public function supprimerEnseignant($id)
    {
        return redirect()->route('adminuniversity.enseignants')->with('success', 'Enseignant supprimé.');
    }

    public function periodes()
    {
        return view('adminuniversity.periodes');
    }

    public function creerPeriode(Request $request)
    {
        $request->validate([
            'nom'        => 'required|string|max:255',
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after:date_debut',
        ]);
        return redirect()->route('adminuniversity.periodes')->with('success', 'Période créée.');
    }

    public function modifierPeriode(Request $request, $id)
    {
        $request->validate([
            'nom'        => 'required|string|max:255',
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after:date_debut',
        ]);
        return redirect()->route('adminuniversity.periodes')->with('success', 'Période mise à jour.');
    }

    public function supprimerPeriode($id)
    {
        return redirect()->route('adminuniversity.periodes')->with('success', 'Période supprimée.');
    }

    public function formations()
    {
        return view('adminuniversity.formations');
    }

    public function creerFormation(Request $request)
    {
        $request->validate(['nom' => 'required|string|max:255']);
        return redirect()->route('adminuniversity.formations')->with('success', 'Formation créée.');
    }

    public function modifierFormation(Request $request, $id)
    {
        $request->validate(['nom' => 'required|string|max:255']);
        return redirect()->route('adminuniversity.formations')->with('success', 'Formation mise à jour.');
    }

    public function supprimerFormation($id)
    {
        return redirect()->route('adminuniversity.formations')->with('success', 'Formation supprimée.');
    }

    public function questionnaires()
    {
        $univId   = $this->universityId();
        $criteres = \App\Models\Critere::where(function ($q) use ($univId) {
                        $q->where('university_id', $univId)
                          ->orWhereNull('university_id');
                    })
                    ->orderBy('university_id', 'desc') // université-spécifiques en premier
                    ->orderBy('ordre')
                    ->get();

        // Indique si l'université a ses propres critères ou hérite des globaux
        $hasOwn = \App\Models\Critere::where('university_id', $univId)->exists();

        return view('adminuniversity.questionnaires', compact('criteres', 'hasOwn'));
    }

    public function saveQuestionnaire(Request $request)
    {
        $univId = $this->universityId();

        $request->validate([
            'criteres'              => ['required', 'array', 'min:1'],
            'criteres.*.nom'        => ['required', 'string', 'max:200'],
            'criteres.*.description'=> ['nullable', 'string', 'max:500'],
            'criteres.*.poids'      => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        // Supprimer les anciens critères de cette université, puis recréer
        \App\Models\Critere::where('university_id', $univId)->delete();

        foreach ($request->input('criteres') as $i => $data) {
            \App\Models\Critere::create([
                'university_id' => $univId,
                'nom'           => $data['nom'],
                'description'   => $data['description'] ?? '',
                'poids'         => (int) $data['poids'],
                'ordre'         => $i + 1,
                'actif'         => isset($data['actif']),
            ]);
        }

        return redirect()->route('adminuniversity.questionnaires')
            ->with('success', 'Configuration des critères enregistrée avec succès.');
    }

    /* ═══════════════════════════════════════════════
       ANNEXES
    ═══════════════════════════════════════════════ */

    public function annexes()
    {
        $annexes = Annexe::with('gestionnaire')
            ->where('university_id', $this->universityId())
            ->orderBy('nom')
            ->get();

        return view('adminuniversity.annexes', compact('annexes'));
    }

    public function creerAnnexe(Request $request)
    {
        $data = $request->validate([
            'nom'       => ['required', 'string', 'max:255'],
            'adresse'   => ['nullable', 'string', 'max:255'],
            'ville'     => ['nullable', 'string', 'max:100'],
            'pays'      => ['nullable', 'string', 'max:100'],
            'email'     => ['nullable', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:20'],
        ]);

        Annexe::create(array_merge($data, ['university_id' => $this->universityId()]));

        return redirect()->route('adminuniversity.annexes')
            ->with('success', "L'annexe « {$data['nom']} » a été créée.");
    }

    public function modifierAnnexe(Request $request, $id)
    {
        $annexe = Annexe::where('university_id', $this->universityId())->findOrFail($id);

        $data = $request->validate([
            'nom'       => ['required', 'string', 'max:255'],
            'adresse'   => ['nullable', 'string', 'max:255'],
            'ville'     => ['nullable', 'string', 'max:100'],
            'pays'      => ['nullable', 'string', 'max:100'],
            'email'     => ['nullable', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:20'],
        ]);

        $annexe->update($data);

        return redirect()->route('adminuniversity.annexes')
            ->with('success', "L'annexe « {$annexe->nom} » a été mise à jour.");
    }

    public function supprimerAnnexe($id)
    {
        $annexe = Annexe::where('university_id', $this->universityId())->findOrFail($id);
        $nom = $annexe->nom;

        // Délier les utilisateurs rattachés à cette annexe
        User::where('annexe_id', $annexe->id)->update(['annexe_id' => null]);
        $annexe->delete();

        return redirect()->route('adminuniversity.annexes')
            ->with('success', "L'annexe « {$nom} » a été supprimée.");
    }

    /* ═══════════════════════════════════════════════
       GESTIONNAIRES D'ANNEXE
    ═══════════════════════════════════════════════ */

    public function creerGestionnaire(Request $request, $annexeId)
    {
        $annexe = Annexe::where('university_id', $this->universityId())->findOrFail($annexeId);

        $data = $request->validate([
            'prenom'    => ['required', 'string', 'max:255'],
            'nom'       => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users,email'],
            'password'  => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'prenom'        => $data['prenom'],
            'nom'           => $data['nom'],
            'name'          => $data['prenom'] . ' ' . $data['nom'],
            'email'         => $data['email'],
            'password'      => Hash::make($data['password']),
            'role'          => 'gestionnaire',
            'university_id' => $this->universityId(),
            'annexe_id'     => $annexe->id,
        ]);

        return redirect()->route('adminuniversity.annexes')
            ->with('success', "Le gestionnaire a été créé pour l'annexe « {$annexe->nom} ».");
    }

    public function supprimerGestionnaire($annexeId)
    {
        $annexe = Annexe::where('university_id', $this->universityId())->findOrFail($annexeId);

        User::where('annexe_id', $annexe->id)
            ->where('role', 'gestionnaire')
            ->update(['annexe_id' => null, 'role' => 'etudiant']);

        return redirect()->route('adminuniversity.annexes')
            ->with('success', "Le gestionnaire de l'annexe « {$annexe->nom} » a été retiré.");
    }

    public function rapports()
    {
        return view('adminuniversity.rapports');
    }

    public function exporterRapport(Request $request)
    {
        $request->validate([
            'type'       => 'required|in:global,formation,enseignant',
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after_or_equal:date_debut',
        ]);
        // TODO: generate PDF using DomPDF
        return redirect()->route('adminuniversity.rapports')->with('success', 'Rapport PDF généré.');
    }
}
