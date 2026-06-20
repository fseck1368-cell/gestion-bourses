<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conventions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('dossier_id')->constrained('dossiers')->onDelete('cascade');
            $table->foreignId('etudiant_id')->constrained('users')->onDelete('cascade');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->decimal('montant_mensuel', 12, 2);
            $table->integer('duree_mois');
            $table->text('conditions')->nullable();
            $table->text('obligations_etudiant')->nullable();
            $table->enum('statut', ['brouillon', 'active', 'suspendue', 'terminee', 'resiliee'])->default('brouillon');
            $table->date('date_signature')->nullable();
            $table->text('motif_resiliation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conventions');
    }
};
