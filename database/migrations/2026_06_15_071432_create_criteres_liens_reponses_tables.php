<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Critères d'évaluation (questions du questionnaire)
        Schema::create('criteres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->nullable()->constrained('universities')->nullOnDelete();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('poids')->default(20); // pourcentage
            $table->unsignedTinyInteger('ordre')->default(0);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        // Liens générés par le gestionnaire (un lien = un questionnaire pour une classe)
        Schema::create('liens_questionnaires', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->foreignId('gestionnaire_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('annexe_id')->constrained('annexes')->cascadeOnDelete();
            $table->string('classe');                                              // ex: "L3 Informatique"
            $table->string('matiere')->nullable();
            $table->foreignId('enseignant_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('titre');
            $table->json('questions');                                             // snapshot critères au moment de la création
            $table->enum('statut', ['actif', 'ferme'])->default('actif');
            $table->timestamp('expire_at')->nullable();
            $table->timestamps();
        });

        // Réponses anonymes soumises via un lien
        Schema::create('reponses_questionnaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lien_questionnaire_id')->constrained('liens_questionnaires')->cascadeOnDelete();
            $table->json('scores');       // [{label: "...", score: 4}, ...]
            $table->text('commentaire')->nullable();
            $table->timestamp('soumis_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reponses_questionnaires');
        Schema::dropIfExists('liens_questionnaires');
        Schema::dropIfExists('criteres');
    }
};
