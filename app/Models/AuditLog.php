<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'details', 'resource', 'resource_id', 'ip_address', 'niveau',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault(['name' => 'Système']);
    }

    public static function write(
        string $action,
        ?string $details = null,
        ?string $resource = null,
        ?int $resourceId = null,
        string $niveau = 'info'
    ): void {
        static::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'details'     => $details,
            'resource'    => $resource,
            'resource_id' => $resourceId,
            'ip_address'  => request()->ip(),
            'niveau'      => $niveau,
        ]);
    }

    public static function actionLabels(): array
    {
        return [
            'inscription_approuvee' => 'Inscription approuvée',
            'inscription_rejetee'   => 'Inscription rejetée',
            'enseignant_cree'       => 'Enseignant créé',
            'enseignant_supprime'   => 'Enseignant supprimé',
            'gestionnaire_cree'     => 'Gestionnaire créé',
            'gestionnaire_supprime' => 'Gestionnaire supprimé',
            'questionnaire_cree'    => 'Questionnaire créé',
            'questionnaire_publie'  => 'Questionnaire publié',
            'evaluation_soumise'    => 'Évaluation soumise',
            'profil_modifie'        => 'Profil modifié',
            'connexion'             => 'Connexion',
        ];
    }

    public function getLabelAction(): string
    {
        return static::actionLabels()[$this->action] ?? $this->action;
    }

    public function getNomUtilisateur(): string
    {
        if (! $this->user) return 'Système';
        $u = $this->user;
        return trim(($u->prenom ?? '') . ' ' . ($u->nom ?? $u->name ?? ''));
    }
}
