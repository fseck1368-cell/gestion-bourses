<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recours', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('dossier_id')->constrained('dossiers')->onDelete('cascade');
            $table->foreignId('etudiant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('traite_par')->nullable()->constrained('users')->nullOnDelete();
            $table->text('motif');
            $table->text('justification')->nullable();
            $table->enum('statut', ['soumis', 'en_examen', 'accepte', 'rejete'])->default('soumis');
            $table->text('decision_motif')->nullable();
            $table->timestamp('date_soumission')->nullable();
            $table->timestamp('date_traitement')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recours');
    }
};
