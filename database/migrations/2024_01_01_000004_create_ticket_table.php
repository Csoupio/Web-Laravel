<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket', function (Blueprint $table) {
            $table->id('ID');
            $table->string('Nom');
            $table->unsignedBigInteger('IDProjet');
            $table->string('Status')->default('Ouvert');
            $table->string('Priorité')->nullable();
            $table->string('Type')->nullable();
            $table->text('Descritpion')->nullable();
            $table->float('Temps_Estime')->default(0);
            $table->timestamps();

            $table->foreign('IDProjet')->references('ID')->on('projets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket');
    }
};
