<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('acronyme')->nullable();
            $table->string('adresse');
            $table->string('ville');
            $table->string('pays')->default('France');
            $table->string('email');
            $table->string('telephone')->nullable();
            $table->string('site_web')->nullable();
            $table->enum('statut', ['en_attente', 'active', 'rejetee'])->default('en_attente');
            $table->foreignId('directeur_id')->constrained('users')->cascadeOnDelete();
            $table->text('motif_rejet')->nullable();
            $table->timestamp('validee_at')->nullable();
            $table->foreignId('validee_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('university_id')->nullable()->after('role')->constrained('universities')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\University::class);
            $table->dropColumn('university_id');
        });
        Schema::dropIfExists('universities');
    }
};
