<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained('dossiers')->onDelete('cascade');
            $table->foreignId('etudiant_id')->constrained('users')->onDelete('cascade');
            $table->string('reference')->unique();
            $table->decimal('montant', 12, 2);
            $table->enum('statut', ['en_attente', 'valide', 'verse', 'annule'])->default('en_attente');
            $table->enum('mode_paiement', ['virement', 'cheque', 'especes'])->default('virement');
            $table->string('reference_bancaire')->nullable();
            $table->string('banque')->nullable();
            $table->string('numero_compte')->nullable();
            $table->date('date_prevue')->nullable();
            $table->date('date_versement')->nullable();
            $table->string('periode')->nullable();
            $table->text('commentaire')->nullable();
            $table->foreignId('valide_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
