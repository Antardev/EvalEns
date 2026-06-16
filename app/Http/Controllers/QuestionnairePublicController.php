<?php

namespace App\Http\Controllers;

use App\Models\Critere;
use App\Models\LienQuestionnaire;
use App\Models\ReponseQuestionnaire;
use Illuminate\Http\Request;

class QuestionnairePublicController extends Controller
{
    /** Critères à utiliser pour un lien (toujours depuis la BD, pas le snapshot). */
    private function criteresPourLien(LienQuestionnaire $lien): \Illuminate\Support\Collection
    {
        $lien->loadMissing('annexe');
        $univId = $lien->annexe->university_id ?? null;
        return Critere::pourUniversite($univId);
    }

    public function show($token)
    {
        $lien = LienQuestionnaire::where('token', $token)
            ->with(['enseignant', 'annexe'])
            ->firstOrFail();

        if (! $lien->isActif()) {
            return view('questionnaire.ferme', compact('lien'));
        }

        $criteres = $this->criteresPourLien($lien);

        return view('questionnaire.show', compact('lien', 'criteres'));
    }

    public function submit(Request $request, $token)
    {
        $lien = LienQuestionnaire::where('token', $token)
            ->with('annexe')
            ->firstOrFail();

        if (! $lien->isActif()) {
            return view('questionnaire.ferme', compact('lien'));
        }

        $criteres = $this->criteresPourLien($lien);

        $rules = [];
        foreach ($criteres as $i => $c) {
            $rules["scores.$i"] = ['required', 'integer', 'between:1,5'];
        }
        $rules['commentaire'] = ['nullable', 'string', 'max:1000'];

        $data = $request->validate($rules);

        $scores = [];
        foreach ($criteres as $i => $c) {
            $scores[] = [
                'label' => $c->nom,
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
