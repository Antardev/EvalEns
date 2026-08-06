<?php

namespace App\Http\Controllers;

use App\Models\Annexe;
use App\Models\Critere;
use App\Models\LienQuestionnaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\EnseignantCredentialsMail;

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

 public function importerEnseignants(Request $request)
{
    $annexe = $this->annexe();

    $data = $request->validate([
        'fichier' => ['required', 'file', 'mimes:xlsx,xls,csv'],
    ]);

    $path = $request->file('fichier')->getRealPath();

    try {
        $spreadsheet = IOFactory::load($path);
    } catch (\Throwable $e) {
        return redirect()
            ->route('gestionnaire.enseignants')
            ->with('error', "Le fichier n'a pas pu être lu : " . $e->getMessage());
    }

    $sheet = $spreadsheet->getActiveSheet();
    $rows  = $sheet->toArray();

    $imported     = 0;
    $skipped      = 0;
    $errors       = [];
    $header       = null;
    $generatedCredentials = []; // [email => ['prenom' => ..., 'password' => ...]]

    DB::beginTransaction();

    try {
        foreach ($rows as $index => $row) {
            $ligneNumero = $index + 1;

            if ($index === 0) {
                $header = array_map(fn($value) => strtolower(trim((string) $value)), $row);
                continue;
            }

            $rowValues = array_values(array_map(fn($value) => trim((string) ($value ?? '')), $row));

            if (empty(array_filter($rowValues, fn($value) => $value !== ''))) {
                continue;
            }

            $values = [];
            foreach ($header as $i => $column) {
                $values[$column] = $rowValues[$i] ?? '';
            }

            $prenom = $values['prenom'] ?? $values['prénom'] ?? $values['first_name'] ?? $values['firstname'] ?? '';
            $nom    = $values['nom'] ?? $values['last_name'] ?? $values['name'] ?? '';
            $email  = $values['email'] ?? $values['mail'] ?? $values['e-mail'] ?? '';

            if ($prenom === '' || $nom === '') {
                $skipped++;
                $errors[] = "Ligne {$ligneNumero} : prénom ou nom manquant.";
                continue;
            }

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skipped++;
                $errors[] = "Ligne {$ligneNumero} : email invalide (\"{$email}\").";
                continue;
            }

            $email = strtolower($email);
            $user = User::where('email', $email)->first();

            if ($user && $user->role && $user->role !== 'enseignant') {
                $skipped++;
                $errors[] = "Ligne {$ligneNumero} : l'email \"{$email}\" appartient déjà à un compte avec le rôle \"{$user->role}\", import ignoré pour cette ligne.";
                continue;
            }

            $isNewUser = ! $user;

            if ($isNewUser) {
                $user = new User();
                $user->email = $email;

                $motDePasseGenere = $this->genererMotDePasse($prenom, $email);
                $user->password = Hash::make($motDePasseGenere);

                $generatedCredentials[$email] = [
                    'prenom'   => $prenom,
                    'password' => $motDePasseGenere,
                ];
            }

            $user->prenom = $prenom;
            $user->nom = $nom;
            $user->name = trim($prenom . ' ' . $nom);
            $user->role = 'enseignant';
            $user->university_id = $annexe->university_id ?? $user->university_id;
            $user->save();

            $user->annexes()->syncWithoutDetaching([$annexe->id]);
            $imported++;
        }

        DB::commit();
    } catch (\Throwable $e) {
        DB::rollBack();
        return redirect()
            ->route('gestionnaire.enseignants')
            ->with('error', "L'import a échoué et a été annulé : " . $e->getMessage());
    }

    $emailsEnErreur = [];

    foreach ($generatedCredentials as $email => $credentials) {
        try {
            Mail::to($email)->queue(new EnseignantCredentialsMail(
                $credentials['prenom'],
                $email,
                $credentials['password'],
                $annexe->nom
            ));
        } catch (\Throwable $e) {
            $emailsEnErreur[] = $email;
            \Log::warning("Échec mise en file d'email pour {$email} : " . $e->getMessage());
        }
    }

    $message = "Import terminé : {$imported} enseignant(s) importé(s), {$skipped} ligne(s) ignorée(s).";

    if (count($generatedCredentials) > 0) {
        $nbEnvoyes = count($generatedCredentials) - count($emailsEnErreur);
        $message .= " {$nbEnvoyes} email(s) d'identifiants mis en file d'envoi.";
    }

    if (! empty($emailsEnErreur)) {
        $errors[] = "Échec d'envoi d'email pour : " . implode(', ', $emailsEnErreur);
    }

    return redirect()
        ->route('gestionnaire.enseignants')
        ->with('success', $message)
        ->with('import_errors', $errors);
}

/**
 * Génère un mot de passe à partir du prénom, de la partie locale de l'email
 * et de chiffres aléatoires. Ex: "jean" + "jdupont" + "4821" => "Jeanjdupont4821"
 */
private function genererMotDePasse(string $prenom, string $email): string
{
    $localPart = strstr($email, '@', true) ?: $email;

    $prenomClean = Str::ascii(trim($prenom));
    $localPartClean = Str::ascii($localPart);

    $prenomClean = preg_replace('/[^a-zA-Z0-9]/', '', $prenomClean);
    $localPartClean = preg_replace('/[^a-zA-Z0-9]/', '', $localPartClean);

    $prenomFormate = ucfirst(strtolower($prenomClean));

    $chiffres = (string) random_int(1000, 9999);

    return $prenomFormate . $localPartClean . $chiffres;
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
        'debut_at'      => ['nullable', 'date'],
        'expire_at'     => ['nullable', 'date', 'after:debut_at'],
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
        'debut_at'        => $data['debut_at'] ?? null,
        'expire_at'       => $data['expire_at'] ?? null,
    ]);

    return redirect()->route('gestionnaire.liens')
        ->with('success', 'Lien questionnaire créé avec succès.');
}

/**
 * Modifie la fenêtre de disponibilité (ouverture / expiration) d'un lien existant.
 */
public function programmerLien(Request $request, $id)
{
    $annexe = $this->annexe();
    $lien   = LienQuestionnaire::where('annexe_id', $annexe->id)->findOrFail($id);

    $data = $request->validate([
        'debut_at'  => ['nullable', 'date'],
        'expire_at' => ['nullable', 'date', 'after:debut_at'],
    ]);

    $lien->debut_at  = $data['debut_at'] ?? null;
    $lien->expire_at = $data['expire_at'] ?? null;
    $lien->save();

    return back()->with('success', 'Programmation du lien mise à jour.');
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

        $hasOwn = Critere::where('university_id', $univId)->exists();

        if ($hasOwn) {
            $criteres = Critere::where('university_id', $univId)
                ->orderBy('ordre')
                ->get();
        } else {
            $criteres = Critere::whereNull('university_id')
                ->orderBy('ordre')
                ->get();
        }

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
