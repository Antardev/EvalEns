<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Creneau extends Model
{
    protected $table = 'creneaux';

    protected $fillable = [
        'emploi_du_temps_id', 'jour', 'heure_debut', 'heure_fin',
        'matiere', 'enseignant_id', 'salle', 'type_cours',
    ];

    public function emploiDuTemps(): BelongsTo
    {
        return $this->belongsTo(EmploiDuTemps::class);
    }

    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }

    public static function jourLabel(int $jour): string
    {
        return ['', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'][$jour] ?? '?';
    }

    public static function typeColor(string $type): string
    {
        return match($type) {
            'td'     => 'badge-warning',
            'tp'     => 'badge-info',
            'examen' => 'badge-danger',
            default  => 'badge-primary',
        };
    }
}
