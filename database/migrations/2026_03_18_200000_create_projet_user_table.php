<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projet_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('projet_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('projet_id')->references('ID')->on('projets')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['projet_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projet_user');
    }
};
