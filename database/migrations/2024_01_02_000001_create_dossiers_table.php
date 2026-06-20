<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dossiers', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('etudiant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('instructeur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('statut', ['soumis', 'en_cours_instruction', 'accepte', 'rejete'])->default('soumis');
            $table->string('annee_universitaire');
            $table->string('niveau_etude');
            $table->string('filiere');
            $table->string('etablissement');
            $table->decimal('moyenne_generale', 5, 2)->nullable();
            $table->text('situation_sociale')->nullable();
            $table->decimal('revenu_familial', 12, 2)->nullable();
            $table->integer('nombre_freres_soeurs')->nullable();
            $table->text('motif_demande')->nullable();
            $table->text('commentaire_instructeur')->nullable();
            $table->text('commentaire_admin')->nullable();
            $table->timestamp('date_soumission')->nullable();
            $table->timestamp('date_instruction')->nullable();
            $table->timestamp('date_decision')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dossiers');
    }
};
