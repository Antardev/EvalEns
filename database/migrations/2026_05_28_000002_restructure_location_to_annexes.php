<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Retirer les colonnes de localisation de universities
        Schema::table('universities', function (Blueprint $table) {
            $table->dropColumn(['adresse', 'ville', 'pays']);
        });

        // Ajouter pays aux annexes
        Schema::table('annexes', function (Blueprint $table) {
            $table->string('pays')->nullable()->after('ville');
        });
    }

    public function down(): void
    {
        Schema::table('annexes', function (Blueprint $table) {
            $table->dropColumn('pays');
        });

        Schema::table('universities', function (Blueprint $table) {
            $table->string('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('pays')->default('France');
        });
    }
};
