<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emplois_du_temps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annexe_id')->constrained('annexes')->cascadeOnDelete();
            $table->date('semaine'); // lundi de la semaine
            $table->enum('statut', ['brouillon', 'publie'])->default('brouillon');
            $table->timestamps();

            $table->unique(['annexe_id', 'semaine']);
        });

        Schema::create('creneaux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emploi_du_temps_id')->constrained('emplois_du_temps')->cascadeOnDelete();
            $table->tinyInteger('jour'); // 1=Lundi … 6=Samedi
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->string('matiere');
            $table->foreignId('enseignant_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('salle')->nullable();
            $table->enum('type_cours', ['cours', 'td', 'tp', 'examen'])->default('cours');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creneaux');
        Schema::dropIfExists('emplois_du_temps');
    }
};
