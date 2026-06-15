<?php

namespace App\Http\Controllers;

use App\Models\Annexe;
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
        return view('adminuniversity.dashboard');
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
        $univId = $this->universityId();
        $annexes = Annexe::where('university_id', $univId)->orderBy('nom')->get();

        $query = User::where('university_id', $univId)->where('role', 'enseignant')
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
        $total   = User::where('university_id', $univId)->where('role', 'enseignant')->count();

        return view('adminuniversity.enseignants', compact('annexes', 'grouped', 'total'));
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
        return view('adminuniversity.questionnaires');
    }

    public function saveQuestionnaire(Request $request)
    {
        // TODO: save questionnaire configuration
        return redirect()->route('adminuniversity.questionnaires')->with('success', 'Questionnaire enregistré.');
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
