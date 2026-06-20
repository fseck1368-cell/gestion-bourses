<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campagnes', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->string('annee_universitaire');
            $table->date('date_ouverture');
            $table->date('date_cloture');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('dossiers', function (Blueprint $table) {
            $table->foreignId('campagne_id')->nullable()->after('reference')->constrained('campagnes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('dossiers', function (Blueprint $table) {
            $table->dropForeign(['campagne_id']);
            $table->dropColumn('campagne_id');
        });
        Schema::dropIfExists('campagnes');
    }
};
