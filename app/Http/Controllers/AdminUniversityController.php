<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Annexe;
use App\Models\LienQuestionnaire;
use App\Models\ReponseQuestionnaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
USE App\Models\Critere;
USE Illuminate\Support\Facades\Mail;


class AdminUniversityController extends Controller
{
    private function universityId(): int
    {
        return Auth::user()->university_id;
    }

    public function dashboard()
    {
        $univId = $this->universityId();

        $nbEnseignants = User::whereHas('annexes', fn($q) => $q->where('university_id', $univId))
            ->where('role', 'enseignant')->count();

        $nbAnnexes = Annexe::where('university_id', $univId)->count();

        $nbEvaluations = ReponseQuestionnaire::whereHas('lien.annexe', fn($q) => $q->where('university_id', $univId))->count();

        $nbLiens = LienQuestionnaire::whereHas('annexe', fn($q) => $q->where('university_id', $univId))->count();

        // Moyennes par annexe pour le graphe
        $annexes = Annexe::where('university_id', $univId)->orderBy('nom')->get();

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
        $enseignantsRecents = User::whereHas('annexes', fn($q) => $q->where('university_id', $univId))
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

    public function enseignantStatistiques($id)
    {
        $univId = $this->universityId();

        $enseignant = User::where('role', 'enseignant')
            ->whereHas('annexes', fn($q) => $q->where('university_id', $univId))
            ->with('annexes')
            ->findOrFail($id);

        $liens = LienQuestionnaire::where('enseignant_id', $id)
            ->whereHas('annexe', fn($q) => $q->where('university_id', $univId))
            ->with(['reponses', 'annexe'])
            ->latest()
            ->get();

        $toutesReponses = $liens->flatMap->reponses;
        $totalReponses  = $toutesReponses->count();
        $totalLiens     = $liens->count();

        $moyenneGlobale = $totalReponses > 0
            ? round($toutesReponses->avg(fn($r) => $r->moyenneGlobale()), 2)
            : null;

        $scoresParCritere = [];
        foreach ($toutesReponses as $reponse) {
            foreach (($reponse->scores ?? []) as $item) {
                $label = $item['label'] ?? '?';
                $scoresParCritere[$label][] = $item['score'] ?? 0;
            }
        }
        $moyennesParCritere = collect($scoresParCritere)
            ->map(fn($s) => round(array_sum($s) / count($s), 2));

        $commentaires = $toutesReponses
            ->filter(fn($r) => !empty($r->commentaire))
            ->sortByDesc('soumis_at')
            ->take(10);

        $statsParLien = $liens->map(function ($lien) {
            $count = $lien->reponses->count();
            return [
                'titre'      => $lien->titre ?: ($lien->matiere ?? 'Sans titre'),
                'classe'     => $lien->classe ?? '—',
                'annexe'     => $lien->annexe->nom ?? '—',
                'statut'     => $lien->statut,
                'expire_at'  => $lien->expire_at,
                'reponses'   => $count,
                'moyenne'    => $count > 0 ? round($lien->reponses->avg(fn($r) => $r->moyenneGlobale()), 2) : null,
                'created_at' => $lien->created_at,
            ];
        });

        return view('adminuniversity.enseignant-statistiques', compact(
            'enseignant', 'totalReponses', 'totalLiens', 'moyenneGlobale',
            'moyennesParCritere', 'commentaires', 'statsParLien'
        ));
    }

    /* ═══════════════════════════════════════════════
       QUESTIONNAIRES / CRITÈRES
    ═══════════════════════════════════════════════ */

   public function questionnaires()
{
    $univId = $this->universityId();

    $hasOwn = Critere::where('university_id', $univId)->exists();

    if ($hasOwn) {
        // L'université a personnalisé : on ne montre QUE ses propres critères
        $criteres = Critere::where('university_id', $univId)
            ->orderBy('ordre')
            ->get();
    } else {
        // Aucune personnalisation : on montre les critères globaux du SuperAdmin
        $criteres = Critere::whereNull('university_id')
            ->orderBy('ordre')
            ->get();
    }

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

        $criteresSoumis = $request->input('criteres', []);

        Critere::where('university_id', $univId)->delete();

        foreach ($criteresSoumis as $i => $data) {
            Critere::create([
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

        $gestionnaire = User::create([
            'prenom'        => $data['prenom'],
            'nom'           => $data['nom'],
            'name'          => $data['prenom'] . ' ' . $data['nom'],
            'email'         => $data['email'],
            'password'      => Hash::make($data['password']),
            'role'          => 'gestionnaire',
            'university_id' => $this->universityId(),
            'annexe_id'     => $annexe->id,
        ]);

        AuditLog::write('gestionnaire_cree', "Gestionnaire « {$gestionnaire->name} » créé pour l'annexe « {$annexe->nom} ».", 'User', $gestionnaire->id);

        return redirect()->route('adminuniversity.annexes')
            ->with('success', "Le gestionnaire a été créé pour l'annexe « {$annexe->nom} ».");
    }

    public function supprimerGestionnaire($annexeId)
    {
        $annexe = Annexe::where('university_id', $this->universityId())->findOrFail($annexeId);

        User::where('annexe_id', $annexe->id)
            ->where('role', 'gestionnaire')
            ->update(['annexe_id' => null]);

        AuditLog::write('gestionnaire_supprime', "Gestionnaire retiré de l'annexe « {$annexe->nom} ».", 'Annexe', $annexe->id, 'warning');

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
