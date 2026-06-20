<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->date('date_deliberation');
            $table->enum('statut', ['planifiee', 'en_cours', 'terminee'])->default('planifiee');
            $table->timestamps();
        });

        Schema::create('commission_membres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_id')->constrained('commissions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_id')->constrained('commissions')->onDelete('cascade');
            $table->foreignId('dossier_id')->constrained('dossiers')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('vote', ['pour', 'contre', 'abstention']);
            $table->text('commentaire')->nullable();
            $table->timestamps();

            $table->unique(['commission_id', 'dossier_id', 'user_id']);
        });

        Schema::create('commission_dossiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_id')->constrained('commissions')->onDelete('cascade');
            $table->foreignId('dossier_id')->constrained('dossiers')->onDelete('cascade');
            $table->string('decision_finale')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_dossiers');
        Schema::dropIfExists('votes');
        Schema::dropIfExists('commission_membres');
        Schema::dropIfExists('commissions');
    }
};
