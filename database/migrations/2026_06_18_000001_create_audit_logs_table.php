<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 100);
            $table->text('details')->nullable();
            $table->string('resource', 100)->nullable();
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->enum('niveau', ['info', 'warning', 'error'])->default('info');
            $table->timestamps();

            $table->index(['action', 'created_at']);
            $table->index('niveau');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
