<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReponseQuestionnaire extends Model
{
    protected $table = 'reponses_questionnaires';

    protected $fillable = ['lien_questionnaire_id', 'scores', 'commentaire', 'soumis_at'];

    protected $casts = [
        'scores'    => 'array',
        'soumis_at' => 'datetime',
    ];

    public function lien(): BelongsTo
    {
        return $this->belongsTo(LienQuestionnaire::class, 'lien_questionnaire_id');
    }

    /** Score moyen global (0-5). */
    public function moyenneGlobale(): float
    {
        if (empty($this->scores)) return 0;
        return round(collect($this->scores)->avg('score'), 2);
    }
}
