<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->foreignId('projet_id')->constrained('projets')->onDelete('cascade');
            $table->string('statut')->default('Ouvert');
            $table->string('priorite')->nullable();
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->float('temps_estime')->default(0);
            
            // Facturation fields
            $table->enum('mode_facturation', ['inclus', 'facturable'])->default('inclus');
            $table->enum('validation_client', ['en_attente', 'accepte', 'refuse'])->nullable();
            $table->text('commentaire_refus')->nullable();
            $table->boolean('facturable_auto')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
