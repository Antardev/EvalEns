<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ajouter le rôle gestionnaire à l'enum users
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin','directeur','enseignant','etudiant','gestionnaire') DEFAULT 'etudiant'");

        Schema::create('annexes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained('universities')->cascadeOnDelete();
            $table->string('nom');
            $table->string('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('annexe_id')->nullable()->after('university_id')->constrained('annexes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['annexe_id']);
            $table->dropColumn('annexe_id');
        });

        Schema::dropIfExists('annexes');

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin','directeur','enseignant','etudiant') DEFAULT 'etudiant'");
    }
};
