<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Critere extends Model
{
    protected $table = 'criteres';

    protected $fillable = ['university_id', 'nom', 'description', 'poids', 'ordre', 'actif'];

    protected $casts = ['actif' => 'boolean'];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    /** Critères actifs pour une université (ou les critères globaux si aucun spécifique). */
    public static function pourUniversite(?int $universityId): \Illuminate\Support\Collection
    {
        $specifiques = static::where('university_id', $universityId)
            ->where('actif', true)->orderBy('ordre')->get();

        if ($specifiques->isNotEmpty()) {
            return $specifiques;
        }

        return static::whereNull('university_id')
            ->where('actif', true)->orderBy('ordre')->get();
    }
}
