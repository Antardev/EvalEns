<?php

namespace Database\Seeders;

use App\Models\Critere;
use Illuminate\Database\Seeder;

class CritereSeeder extends Seeder
{
    public function run(): void
    {
        // Supprimer les anciens critères globaux avant de réinsérer
        Critere::whereNull('university_id')->delete();

        $criteres = [
            ['nom' => 'Degré de satisfaction',                          'description' => '',  'poids' => 8,  'ordre' => 1],
            ['nom' => 'Organisation du cours',                          'description' => '',  'poids' => 7,  'ordre' => 2],
            ['nom' => 'Gestion du temps',                               'description' => '',  'poids' => 7,  'ordre' => 3],
            ['nom' => 'Traçabilité',                                    'description' => '',  'poids' => 7,  'ordre' => 4],
            ['nom' => 'Qualité de l\'animation',                        'description' => '',  'poids' => 7,  'ordre' => 5],
            ['nom' => 'Interaction avec les étudiants',                 'description' => '',  'poids' => 7,  'ordre' => 6],
            ['nom' => 'Indication sur le déroulement de l\'évaluation', 'description' => '',  'poids' => 7,  'ordre' => 7],
            ['nom' => 'Cohérence / Clarté du cours',                    'description' => '',  'poids' => 7,  'ordre' => 8],
            ['nom' => 'Pragmatisme',                                    'description' => '',  'poids' => 7,  'ordre' => 9],
            ['nom' => 'Présentation de l\'enseignant',                  'description' => '',  'poids' => 7,  'ordre' => 10],
            ['nom' => 'Qualité des outils et des supports',             'description' => '',  'poids' => 7,  'ordre' => 11],
            ['nom' => 'Qualité pédagogique',                            'description' => '',  'poids' => 8,  'ordre' => 12],
            ['nom' => 'Adéquation cours et les sujets de contrôle',     'description' => '',  'poids' => 7,  'ordre' => 13],
            ['nom' => 'Relation avec l\'étudiant(e)',                   'description' => '',  'poids' => 7,  'ordre' => 14],
        ];

        foreach ($criteres as $data) {
            Critere::create(array_merge($data, ['actif' => true, 'university_id' => null]));
        }
    }
}
