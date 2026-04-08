<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ajoute un budget d'heures contrat sur chaque projet
        Schema::table('projets', function (Blueprint $table) {
            $table->float('contrat_heures')->default(0)->after('Description');
        });

        // Enrichit chaque ticket avec son mode de facturation et son statut de validation
        Schema::table('ticket', function (Blueprint $table) {
            // 'inclus' = consomme les heures contrat | 'facturable' = supplément
            $table->enum('mode_facturation', ['inclus', 'facturable'])->default('inclus')->after('Temps_Estime');

            // null = pas encore soumis | 'en_attente' | 'accepte' | 'refuse'
            $table->enum('validation_client', ['en_attente', 'accepte', 'refuse'])->nullable()->after('mode_facturation');

            // Commentaire du client en cas de refus
            $table->text('commentaire_refus')->nullable()->after('validation_client');

            // true = passage en facturable déclenché automatiquement (contrat épuisé)
            $table->boolean('facturable_auto')->default(false)->after('commentaire_refus');
        });
    }

    public function down(): void
    {
        Schema::table('projets', function (Blueprint $table) {
            $table->dropColumn('contrat_heures');
        });

        Schema::table('ticket', function (Blueprint $table) {
            $table->dropColumn(['mode_facturation', 'validation_client', 'commentaire_refus', 'facturable_auto']);
        });
    }
};
