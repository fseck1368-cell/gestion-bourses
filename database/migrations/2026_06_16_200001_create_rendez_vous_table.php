<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained('dossiers')->onDelete('cascade');
            $table->foreignId('etudiant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('instructeur_id')->constrained('users')->onDelete('cascade');
            $table->datetime('date_heure');
            $table->string('lieu')->nullable();
            $table->string('motif');
            $table->enum('statut', ['demande', 'confirme', 'refuse', 'termine', 'annule'])->default('demande');
            $table->text('note_instructeur')->nullable();
            $table->text('commentaire_refus')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rendez_vous');
    }
};
