<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained('dossiers')->onDelete('cascade');
            $table->foreignId('critere_id')->constrained('criteres_eligibilite')->onDelete('cascade');
            $table->foreignId('evaluateur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('note', 5, 2)->nullable();
            $table->boolean('critere_rempli')->default(false);
            $table->text('commentaire')->nullable();
            $table->timestamps();

            $table->unique(['dossier_id', 'critere_id']);
        });

        Schema::table('dossiers', function (Blueprint $table) {
            $table->decimal('score_global', 5, 2)->nullable()->after('commentaire_admin');
        });
    }

    public function down(): void
    {
        Schema::table('dossiers', function (Blueprint $table) {
            $table->dropColumn('score_global');
        });
        Schema::dropIfExists('evaluations');
    }
};
