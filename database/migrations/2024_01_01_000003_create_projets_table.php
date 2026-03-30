<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projets', function (Blueprint $table) {
            $table->id('ID');
            $table->string('Nom');
            $table->unsignedBigInteger('ClientsID');
            $table->text('Description')->nullable();
            $table->timestamps();

            $table->foreign('ClientsID')->references('ID')->on('clients')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projets');
    }
};
