<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criteres_eligibilite', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campagne_id')->constrained('campagnes')->onDelete('cascade');
            $table->string('nom');
            $table->text('description')->nullable();
            $table->enum('type', ['numerique', 'booleen', 'selection']);
            $table->decimal('valeur_min', 10, 2)->nullable();
            $table->decimal('valeur_max', 10, 2)->nullable();
            $table->json('valeurs_acceptees')->nullable();
            $table->integer('poids')->default(1);
            $table->boolean('obligatoire')->default(true);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criteres_eligibilite');
    }
};
