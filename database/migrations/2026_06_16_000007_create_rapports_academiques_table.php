<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rapports_academiques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained('dossiers')->onDelete('cascade');
            $table->foreignId('etudiant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('convention_id')->nullable()->constrained('conventions')->nullOnDelete();
            $table->string('semestre');
            $table->string('annee_universitaire');
            $table->decimal('moyenne', 5, 2)->nullable();
            $table->integer('credits_valides')->nullable();
            $table->integer('credits_total')->nullable();
            $table->decimal('taux_assiduite', 5, 2)->nullable();
            $table->enum('statut_academique', ['bon', 'acceptable', 'insuffisant', 'exclus'])->default('bon');
            $table->boolean('renouvellement_recommande')->default(false);
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapports_academiques');
    }
};
