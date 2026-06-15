<?php

namespace App\Http\Controllers;

use App\Models\Annexe;
use App\Models\Critere;
use App\Models\Creneau;
use App\Models\EmploiDuTemps;
use App\Models\LienQuestionnaire;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GestionnaireController extends Controller
{
    private function annexe(): Annexe
    {
        return Annexe::with('university')
            ->findOrFail(Auth::user()->annexe_id);
    }

    public function dashboard()
    {
        $annexe = $this->annexe();

        $stats = [
            'etudiants'   => User::where('annexe_id', $annexe->id)->where('role', 'etudiant')->count(),
            'enseignants' => User::where('annexe_id', $annexe->id)->where('role', 'enseignant')->count(),
            'liens'       => LienQuestionnaire::where('annexe_id', $annexe->id)->count(),
            'reponses'    => \App\Models\ReponseQuestionnaire::whereHas('lien', fn($q) => $q->where('annexe_id', $annexe->id))->count(),
        ];

        return view('gestionnaire.dashboard', compact('annexe', 'stats'));
    }

    public function etudiants(Request $request)
    {
        $annexe = $this->annexe();

        $query = User::where('annexe_id', $annexe->id)->where('role', 'etudiant')->latest();

        if ($search = $request->input('search')) {
            $query->where(fn($q) => $q->where('prenom', 'like', "%$search%")
                                      ->orWhere('nom', 'like', "%$search%")
                                      ->orWhere('email', 'like', "%$search%"));
        }

        $membres = $query->paginate(20)->withQueryString();
        $total   = User::where('annexe_id', $annexe->id)->where('role', 'etudiant')->count();

        return view('gestionnaire.etudiants', compact('annexe', 'membres', 'total'));
    }

    public function enseignants(Request $request)
    {
        $annexe = $this->annexe();

        $query = User::where('annexe_id', $annexe->id)->where('role', 'enseignant')->latest();

        if ($search = $request->input('search')) {
            $query->where(fn($q) => $q->where('prenom', 'like', "%$search%")
                                      ->orWhere('nom', 'like', "%$search%")
                                      ->orWhere('email', 'like', "%$search%"));
        }

        $membres = $query->paginate(20)->withQueryString();
        $total   = User::where('annexe_id', $annexe->id)->where('role', 'enseignant')->count();

        return view('gestionnaire.enseignants', compact('annexe', 'membres', 'total'));
    }

    /* ═══════════════════════════════════════════════
       EMPLOIS DU TEMPS
    ═══════════════════════════════════════════════ */

    public function emploisDuTemps()
    {
        $annexe  = $this->annexe();
        $emplois = EmploiDuTemps::where('annexe_id', $annexe->id)
            ->withCount('creneaux')
            ->orderByDesc('semaine')
            ->get();

        return view('gestionnaire.emplois-du-temps', compact('annexe', 'emplois'));
    }

    public function creerEmploiDuTemps(Request $request)
    {
        $annexe = $this->annexe();

        $data = $request->validate(['semaine' => ['required', 'date']]);

        $lundi = Carbon::parse($data['semaine'])->startOfWeek(Carbon::MONDAY)->toDateString();

        $emploi = EmploiDuTemps::firstOrCreate(
            ['annexe_id' => $annexe->id, 'semaine' => $lundi],
            ['statut' => 'brouillon']
        );

        return redirect()->route('gestionnaire.emplois-du-temps.voir', $emploi->id)
            ->with('success', 'Emploi du temps créé.');
    }

    public function voirEmploiDuTemps($id)
    {
        $annexe  = $this->annexe();
        $emploi  = EmploiDuTemps::where('annexe_id', $annexe->id)
            ->with(['creneaux.enseignant'])
            ->findOrFail($id);

        $enseignants = User::where('annexe_id', $annexe->id)
            ->where('role', 'enseignant')
            ->orderBy('nom')->get();

        $jours = collect(range(1, 6))->mapWithKeys(fn($j) => [
            $j => [
                'label'    => Creneau::jourLabel($j),
                'date'     => $emploi->semaine->copy()->addDays($j - 1),
                'creneaux' => $emploi->creneaux->where('jour', $j)->values(),
            ]
        ]);

        return view('gestionnaire.emploi-du-temps', compact('annexe', 'emploi', 'jours', 'enseignants'));
    }

    public function publierEmploiDuTemps($id)
    {
        $annexe = $this->annexe();
        $emploi = EmploiDuTemps::where('annexe_id', $annexe->id)->findOrFail($id);

        $emploi->statut = $emploi->isPublie() ? 'brouillon' : 'publie';
        $emploi->save();

        $msg = $emploi->isPublie() ? 'Emploi du temps publié.' : 'Emploi du temps repassé en brouillon.';
        return back()->with('success', $msg);
    }

    public function supprimerEmploiDuTemps($id)
    {
        $annexe = $this->annexe();
        EmploiDuTemps::where('annexe_id', $annexe->id)->findOrFail($id)->delete();

        return redirect()->route('gestionnaire.emplois-du-temps')
            ->with('success', 'Emploi du temps supprimé.');
    }

    public function creerCreneau(Request $request, $emploiId)
    {
        $annexe = $this->annexe();
        $emploi = EmploiDuTemps::where('annexe_id', $annexe->id)->findOrFail($emploiId);

        $data = $request->validate([
            'jour'          => ['required', 'integer', 'between:1,6'],
            'heure_debut'   => ['required', 'date_format:H:i'],
            'heure_fin'     => ['required', 'date_format:H:i', 'after:heure_debut'],
            'matiere'       => ['required', 'string', 'max:100'],
            'enseignant_id' => ['nullable', 'exists:users,id'],
            'salle'         => ['nullable', 'string', 'max:50'],
            'type_cours'    => ['required', 'in:cours,td,tp,examen'],
        ]);

        $emploi->creneaux()->create($data);
        return back()->with('success', 'Créneau ajouté.');
    }

    public function supprimerCreneau($creneauId)
    {
        $annexe  = $this->annexe();
        $creneau = Creneau::whereHas('emploiDuTemps', fn($q) => $q->where('annexe_id', $annexe->id))
            ->findOrFail($creneauId);

        $emploiId = $creneau->emploi_du_temps_id;
        $creneau->delete();

        return redirect()->route('gestionnaire.emplois-du-temps.voir', $emploiId)
            ->with('success', 'Créneau supprimé.');
    }

    /* ═══════════════════════════════════════════════
       LIENS QUESTIONNAIRES
    ═══════════════════════════════════════════════ */

    public function liens(Request $request)
    {
        $annexe = $this->annexe();

        $liens = LienQuestionnaire::where('annexe_id', $annexe->id)
            ->with(['enseignant', 'reponses'])
            ->withCount('reponses')
            ->latest()
            ->get();

        $enseignants = User::where('annexe_id', $annexe->id)
            ->where('role', 'enseignant')
            ->orderBy('nom')->get();

        $criteres = Critere::pourUniversite($annexe->university_id ?? null);

        return view('gestionnaire.liens', compact('annexe', 'liens', 'enseignants', 'criteres'));
    }

    public function creerLien(Request $request)
    {
        $annexe = $this->annexe();

        $data = $request->validate([
            'classe'        => ['required', 'string', 'max:100'],
            'matiere'       => ['nullable', 'string', 'max:100'],
            'enseignant_id' => ['nullable', 'exists:users,id'],
            'titre'         => ['required', 'string', 'max:200'],
            'expire_at'     => ['nullable', 'date', 'after:now'],
        ]);

        // Snapshot des critères actifs
        $criteres = Critere::pourUniversite($annexe->university_id ?? null);
        $questions = $criteres->map(fn($c) => [
            'id'          => $c->id,
            'label'       => $c->nom,
            'description' => $c->description,
        ])->values()->toArray();

        LienQuestionnaire::create([
            'token'          => LienQuestionnaire::genererToken(),
            'gestionnaire_id'=> Auth::id(),
            'annexe_id'      => $annexe->id,
            'classe'         => $data['classe'],
            'matiere'        => $data['matiere'] ?? null,
            'enseignant_id'  => $data['enseignant_id'] ?? null,
            'titre'          => $data['titre'],
            'questions'      => $questions,
            'statut'         => 'actif',
            'expire_at'      => $data['expire_at'] ?? null,
        ]);

        return redirect()->route('gestionnaire.liens')
            ->with('success', 'Lien questionnaire créé avec succès.');
    }

    public function fermerLien($id)
    {
        $annexe = $this->annexe();
        $lien   = LienQuestionnaire::where('annexe_id', $annexe->id)->findOrFail($id);

        $lien->statut = $lien->statut === 'actif' ? 'ferme' : 'actif';
        $lien->save();

        $msg = $lien->statut === 'actif' ? 'Lien réouvert.' : 'Lien fermé.';
        return back()->with('success', $msg);
    }

    public function supprimerLien($id)
    {
        $annexe = $this->annexe();
        LienQuestionnaire::where('annexe_id', $annexe->id)->findOrFail($id)->delete();

        return back()->with('success', 'Lien supprimé.');
    }

    public function voirReponses($id)
    {
        $annexe  = $this->annexe();
        $lien    = LienQuestionnaire::where('annexe_id', $annexe->id)
            ->with(['enseignant', 'reponses'])
            ->findOrFail($id);

        $reponses = $lien->reponses()->latest('soumis_at')->get();

        // Calculer la moyenne par critère
        $moyennes = [];
        if ($reponses->isNotEmpty()) {
            foreach ($lien->questions as $question) {
                $label  = $question['label'];
                $scores = $reponses->map(fn($r) => collect($r->scores)->firstWhere('label', $label)['score'] ?? null)
                    ->filter()->values();
                $moyennes[$label] = $scores->isNotEmpty() ? round($scores->avg(), 2) : null;
            }
        }

        return view('gestionnaire.reponses', compact('annexe', 'lien', 'reponses', 'moyennes'));
    }
}
