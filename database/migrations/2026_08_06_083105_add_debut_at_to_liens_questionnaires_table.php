<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('liens_questionnaires', function (Blueprint $table) {
            $table->timestamp('debut_at')->nullable()->after('statut');
        });
    }

    public function down(): void
    {
        Schema::table('liens_questionnaires', function (Blueprint $table) {
            $table->dropColumn('debut_at');
        });
    }
};
