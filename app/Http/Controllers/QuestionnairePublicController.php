<?php

namespace App\Http\Controllers;

use App\Models\LienQuestionnaire;
use App\Models\ReponseQuestionnaire;
use Illuminate\Http\Request;

class QuestionnairePublicController extends Controller
{
    public function show($token)
    {
        $lien = LienQuestionnaire::where('token', $token)
            ->with('enseignant')
            ->firstOrFail();

        if (! $lien->isActif()) {
            return view('questionnaire.ferme', compact('lien'));
        }

        return view('questionnaire.show', compact('lien'));
    }

    public function submit(Request $request, $token)
    {
        $lien = LienQuestionnaire::where('token', $token)->firstOrFail();

        if (! $lien->isActif()) {
            return view('questionnaire.ferme', compact('lien'));
        }

        // Valider les scores (un par question)
        $questions = $lien->questions;
        $rules = [];
        foreach ($questions as $i => $q) {
            $rules["scores.$i"] = ['required', 'integer', 'between:1,5'];
        }
        $rules['commentaire'] = ['nullable', 'string', 'max:1000'];

        $data = $request->validate($rules);

        // Construire le tableau scores [{label, score}]
        $scores = [];
        foreach ($questions as $i => $q) {
            $scores[] = [
                'label' => $q['label'],
                'score' => (int) $data['scores'][$i],
            ];
        }

        ReponseQuestionnaire::create([
            'lien_questionnaire_id' => $lien->id,
            'scores'                => $scores,
            'commentaire'           => $data['commentaire'] ?? null,
            'soumis_at'             => now(),
        ]);

        return view('questionnaire.merci', compact('lien'));
    }
}
