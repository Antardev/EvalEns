<?php

namespace App\Http\Controllers;

use App\Models\LienQuestionnaire;
use App\Models\ReponseQuestionnaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    /** Tous les liens de ce prof avec leurs réponses chargées. */
    private function liens()
    {
        return LienQuestionnaire::where('enseignant_id', Auth::id())
            ->with('reponses')
            ->withCount('reponses')
            ->latest()
            ->get();
    }

    /** Calcule moyenne globale (1–5) et par critère à partir d'une collection de liens. */
    private function stats($liens): array
    {
        $allScores     = [];
        $byLabel       = [];

        foreach ($liens as $lien) {
            foreach ($lien->reponses as $rep) {
                foreach ($rep->scores as $s) {
                    $allScores[]          = $s['score'];
                    $byLabel[$s['label']][] = $s['score'];
                }
            }
        }

        $moyenneGlobale = count($allScores)
            ? round(array_sum($allScores) / count($allScores), 2)
            : null;

        $parCritere = [];
        foreach ($byLabel as $label => $scores) {
            $parCritere[$label] = round(array_sum($scores) / count($scores), 2);
        }

        return [
            'moyenne'    => $moyenneGlobale,
            'parCritere' => $parCritere,
            'total'      => count(array_unique(array_column(
                array_merge(...array_map(
                    fn($l) => $l->reponses->toArray(),
                    $liens->all()
                )),
                'id'
            ))),
        ];
    }

    public function dashboard()
    {
        $liens         = $this->liens();
        $totalReponses = $liens->sum('reponses_count');
        $liensActifs   = $liens->where('statut', 'actif')->count();

        // Stats globales
        $allScores  = [];
        $byLabel    = [];

        foreach ($liens as $lien) {
            foreach ($lien->reponses as $rep) {
                foreach ($rep->scores as $s) {
                    $allScores[]            = $s['score'];
                    $byLabel[$s['label']][] = $s['score'];
                }
            }
        }

        $moyenneGlobale = count($allScores)
            ? round(array_sum($allScores) / count($allScores), 2)
            : null;

        $parCritere = [];
        foreach ($byLabel as $label => $scores) {
            $parCritere[$label] = round(array_sum($scores) / count($scores), 2);
        }

        // 5 derniers liens avec activité
        $derniersLiens = $liens->take(5);

        return view('teacher.dashboard', compact(
            'liens', 'totalReponses', 'liensActifs',
            'moyenneGlobale', 'parCritere', 'derniersLiens'
        ));
    }

    public function resultats()
    {
        $liens = $this->liens();

        // Par lien : moyenne globale + par critère
        $statsParLien = [];
        foreach ($liens as $lien) {
            $allScores = [];
            $byLabel   = [];
            foreach ($lien->reponses as $rep) {
                foreach ($rep->scores as $s) {
                    $allScores[]            = $s['score'];
                    $byLabel[$s['label']][] = $s['score'];
                }
            }
            $parCritere = [];
            foreach ($byLabel as $label => $scores) {
                $parCritere[$label] = round(array_sum($scores) / count($scores), 2);
            }
            $statsParLien[$lien->id] = [
                'moyenne'    => count($allScores) ? round(array_sum($allScores) / count($allScores), 2) : null,
                'parCritere' => $parCritere,
            ];
        }

        return view('teacher.resultats', compact('liens', 'statsParLien'));
    }

    public function commentaires(Request $request)
    {
        $query = ReponseQuestionnaire::whereHas('lien', fn($q) => $q->where('enseignant_id', Auth::id()))
            ->whereNotNull('commentaire')
            ->where('commentaire', '!=', '')
            ->with('lien')
            ->latest('soumis_at');

        if ($lienId = $request->input('lien_id')) {
            $query->where('lien_questionnaire_id', $lienId);
        }

        $commentaires = $query->paginate(20)->withQueryString();

        $mesLiens = LienQuestionnaire::where('enseignant_id', Auth::id())
            ->orderBy('titre')->get();

        return view('teacher.commentaires', compact('commentaires', 'mesLiens'));
    }

    public function evolution()
    {
        return view('teacher.evolution');
    }

    public function rapport()
    {
        return view('teacher.rapport');
    }

    public function exporterRapport(Request $request)
    {
        $request->validate(['periode_id' => 'required']);
        return redirect()->route('teacher.rapport')->with('success', 'Rapport PDF généré.');
    }
}
