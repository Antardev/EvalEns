<?php

namespace Database\Seeders;

use App\Models\Critere;
use Illuminate\Database\Seeder;

class CritereSeeder extends Seeder
{
    public function run(): void
    {
        $criteres = [
            ['nom' => 'Qualité pédagogique',    'description' => 'Clarté des explications, maîtrise du contenu, exemples pertinents.',   'poids' => 30, 'ordre' => 1],
            ['nom' => 'Organisation du cours',  'description' => 'Structuration des séances, respect du programme et du timing.',        'poids' => 25, 'ordre' => 2],
            ['nom' => 'Communication',           'description' => 'Capacité à transmettre, écoute des étudiants, clarté du langage.',     'poids' => 20, 'ordre' => 3],
            ['nom' => 'Disponibilité',           'description' => 'Accessibilité en dehors des cours, réactivité aux questions.',        'poids' => 15, 'ordre' => 4],
            ['nom' => 'Équité et impartialité', 'description' => 'Traitement équitable de tous les étudiants, justice dans la notation.', 'poids' => 10, 'ordre' => 5],
        ];

        foreach ($criteres as $data) {
            Critere::firstOrCreate(
                ['nom' => $data['nom'], 'university_id' => null],
                array_merge($data, ['actif' => true, 'university_id' => null])
            );
        }
    }
}
