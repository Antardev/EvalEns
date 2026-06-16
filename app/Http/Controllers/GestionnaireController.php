<?php

namespace App\Http\Controllers;

use App\Models\Annexe;
use App\Models\Critere;
use App\Models\LienQuestionnaire;
use App\Models\User;
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
            'enseignants' => User::whereHas('annexes', fn($q) => $q->where('annexes.id', $annexe->id))->where('role', 'enseignant')->count(),
            'liens'       => LienQuestionnaire::where('annexe_id', $annexe->id)->count(),
            'reponses'    => \App\Models\ReponseQuestionnaire::whereHas('lien', fn($q) => $q->where('annexe_id', $annexe->id))->count(),
        ];

        return view('gestionnaire.dashboard', compact('annexe', 'stats'));
    }

    public function enseignants(Request $request)
    {
        $annexe = $this->annexe();

        $query = User::whereHas('annexes', fn($q) => $q->where('annexes.id', $annexe->id))
            ->where('role', 'enseignant')->latest();

        if ($search = $request->input('search')) {
            $query->where(fn($q) => $q->where('prenom', 'like', "%$search%")
                                      ->orWhere('nom', 'like', "%$search%")
                                      ->orWhere('email', 'like', "%$search%"));
        }

        $membres = $query->paginate(20)->withQueryString();
        $total   = User::whereHas('annexes', fn($q) => $q->where('annexes.id', $annexe->id))->where('role', 'enseignant')->count();

        return view('gestionnaire.enseignants', compact('annexe', 'membres', 'total'));
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

        $enseignants = User::whereHas('annexes', fn($q) => $q->where('annexes.id', $annexe->id))
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

        $criteres  = Critere::pourUniversite($annexe->university_id ?? null);
        $questions = $criteres->map(fn($c) => [
            'id'          => $c->id,
            'label'       => $c->nom,
            'description' => $c->description,
        ])->values()->toArray();

        LienQuestionnaire::create([
            'token'           => LienQuestionnaire::genererToken(),
            'gestionnaire_id' => Auth::id(),
            'annexe_id'       => $annexe->id,
            'classe'          => $data['classe'],
            'matiere'         => $data['matiere'] ?? null,
            'enseignant_id'   => $data['enseignant_id'] ?? null,
            'titre'           => $data['titre'],
            'questions'       => $questions,
            'statut'          => 'actif',
            'expire_at'       => $data['expire_at'] ?? null,
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

        return back()->with('success', $lien->statut === 'actif' ? 'Lien réouvert.' : 'Lien fermé.');
    }

    public function supprimerLien($id)
    {
        $annexe = $this->annexe();
        LienQuestionnaire::where('annexe_id', $annexe->id)->findOrFail($id)->delete();

        return back()->with('success', 'Lien supprimé.');
    }

    public function questionnaires()
    {
        $annexe = $this->annexe();
        $univId = $annexe->university_id ?? null;

        $criteres = Critere::where(function ($q) use ($univId) {
                        $q->where('university_id', $univId)
                          ->orWhereNull('university_id');
                    })
                    ->orderBy('university_id', 'desc')
                    ->orderBy('ordre')
                    ->get();

        $hasOwn = Critere::where('university_id', $univId)->exists();

        return view('gestionnaire.questionnaires', compact('annexe', 'criteres', 'hasOwn'));
    }

    public function saveQuestionnaire(\Illuminate\Http\Request $request)
    {
        $annexe = $this->annexe();
        $univId = $annexe->university_id ?? null;

        $request->validate([
            'criteres'               => ['required', 'array', 'min:1'],
            'criteres.*.nom'         => ['required', 'string', 'max:200'],
            'criteres.*.description' => ['nullable', 'string', 'max:500'],
            'criteres.*.poids'       => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        Critere::where('university_id', $univId)->delete();

        foreach ($request->input('criteres') as $i => $data) {
            Critere::create([
                'university_id' => $univId,
                'nom'           => $data['nom'],
                'description'   => $data['description'] ?? '',
                'poids'         => (int) $data['poids'],
                'ordre'         => $i + 1,
                'actif'         => isset($data['actif']),
            ]);
        }

        return redirect()->route('gestionnaire.questionnaires')
            ->with('success', 'Configuration des critères enregistrée.');
    }

    public function rafraichirLien($id)
    {
        $annexe = $this->annexe();
        $lien   = LienQuestionnaire::where('annexe_id', $annexe->id)->findOrFail($id);

        if ($lien->reponses()->count() > 0) {
            return back()->with('error', 'Impossible de rafraîchir : ce lien a déjà des réponses.');
        }

        $criteres  = Critere::pourUniversite($annexe->university_id ?? null);
        $lien->questions = $criteres->map(fn($c) => [
            'id'          => $c->id,
            'label'       => $c->nom,
            'description' => $c->description,
        ])->values()->toArray();
        $lien->save();

        return back()->with('success', 'Critères mis à jour — ' . $criteres->count() . ' critères chargés.');
    }

    public function voirReponses($id)
    {
        $annexe   = $this->annexe();
        $lien     = LienQuestionnaire::where('annexe_id', $annexe->id)
            ->with(['enseignant', 'reponses'])
            ->findOrFail($id);

        $reponses = $lien->reponses()->latest('soumis_at')->get();

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
