<?php

namespace App\Http\Controllers;

use App\Models\Creneau;
use App\Models\EmploiDuTemps;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard()
    {
        return view('student.dashboard');
    }

    public function evaluations()
    {
        return view('student.evaluations');
    }

    public function evaluationForm($token)
    {
        // TODO: find evaluation by token, verify not already submitted
        return view('student.evaluation-form', compact('token'));
    }

    public function soumettre(Request $request, $token)
    {
        $request->validate([
            'scores'   => 'required|array|min:1',
            'scores.*' => 'required|integer|between:1,5',
        ]);
        // TODO: save evaluation and mark token as used
        return redirect()->route('student.historique')->with('success', 'Évaluation soumise avec succès. Merci !');
    }

    public function saveBrouillon(Request $request, $token)
    {
        // TODO: save draft to session or database
        return response()->json(['success' => true, 'message' => 'Brouillon enregistré.']);
    }

    public function historique()
    {
        return view('student.historique');
    }

    public function emploiDuTemps(Request $request)
    {
        $user     = Auth::user();
        $annexeId = $user->annexe_id;

        $semaine = $request->input('semaine')
            ? Carbon::parse($request->input('semaine'))->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);

        $emploi = EmploiDuTemps::where('annexe_id', $annexeId)
            ->where('semaine', $semaine->toDateString())
            ->where('statut', 'publie')
            ->with(['creneaux.enseignant'])
            ->first();

        $jours = collect(range(1, 6))->mapWithKeys(fn($j) => [
            $j => [
                'label'    => Creneau::jourLabel($j),
                'date'     => $semaine->copy()->addDays($j - 1),
                'creneaux' => $emploi ? $emploi->creneaux->where('jour', $j)->values() : collect(),
            ]
        ]);

        return view('student.emploi-du-temps', compact('emploi', 'jours', 'semaine'));
    }
}
