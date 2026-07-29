<?php

namespace Tests\Feature;

use App\Models\Annexe;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GestionnaireImportEnseignantsTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_gestionnaire_peut_importer_des_enseignants_avec_un_mot_de_passe_partage(): void
    {
        $university = University::create([
            'nom' => 'Université Test',
            'acronyme' => 'UT',
            'statut' => 'active',
        ]);

        $annexe = Annexe::create([
            'university_id' => $university->id,
            'nom' => 'Annexe Test',
            'ville' => 'Paris',
        ]);

        $gestionnaire = User::create([
            'prenom' => 'Alice',
            'nom' => 'Martin',
            'name' => 'Alice Martin',
            'email' => 'gestionnaire@example.com',
            'password' => 'secret123',
            'role' => 'gestionnaire',
            'university_id' => $university->id,
            'annexe_id' => $annexe->id,
        ]);

        $file = tempnam(sys_get_temp_dir(), 'excel');
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['Prénom', 'Nom', 'Email'],
            ['Jean', 'Dupont', 'jean.dupont@example.com'],
            ['Marie', 'Durand', 'marie.durand@example.com'],
        ], null, 'A1');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($file);

        $response = $this->actingAs($gestionnaire)->post(route('gestionnaire.enseignants.importer'), [
            'fichier' => new \Illuminate\Http\UploadedFile($file, 'enseignants.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true),
            'password' => 'MotDePasse123!',
        ]);

        $response->assertRedirect(route('gestionnaire.enseignants'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'jean.dupont@example.com',
            'role' => 'enseignant',
        ]);

        $jean = User::where('email', 'jean.dupont@example.com')->first();
        $this->assertTrue(Hash::check('MotDePasse123!', $jean->password));
        $this->assertTrue($jean->annexes()->where('annexes.id', $annexe->id)->exists());

        unlink($file);
    }
}
