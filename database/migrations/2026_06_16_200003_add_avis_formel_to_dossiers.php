<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dossiers', function (Blueprint $table) {
            $table->enum('avis_instructeur', ['favorable', 'defavorable', 'reserve'])->nullable()->after('commentaire_instructeur');
            $table->timestamp('date_avis_instructeur')->nullable()->after('avis_instructeur');
            $table->boolean('avis_transmis_admin')->default(false)->after('date_avis_instructeur');
        });
    }

    public function down(): void
    {
        Schema::table('dossiers', function (Blueprint $table) {
            $table->dropColumn(['avis_instructeur', 'date_avis_instructeur', 'avis_transmis_admin']);
        });
    }
};
