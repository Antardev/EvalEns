<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enseignant_annexes', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('annexe_id')->constrained('annexes')->cascadeOnDelete();
            $table->primary(['user_id', 'annexe_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enseignant_annexes');
    }
};
