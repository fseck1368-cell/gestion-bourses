<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dossiers', function (Blueprint $table) {
            $table->boolean('complement_requis')->default(false)->after('statut');
            $table->text('complement_description')->nullable()->after('complement_requis');
            $table->timestamp('complement_date_demande')->nullable()->after('complement_description');
            $table->timestamp('complement_date_reponse')->nullable()->after('complement_date_demande');
        });
    }

    public function down(): void
    {
        Schema::table('dossiers', function (Blueprint $table) {
            $table->dropColumn(['complement_requis', 'complement_description', 'complement_date_demande', 'complement_date_reponse']);
        });
    }
};
