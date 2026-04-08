<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_entries', function (Blueprint $table) {
            $table->id('ID');
            $table->unsignedBigInteger('IDTicket');
            $table->unsignedBigInteger('IDUser');
            $table->date('date');
            $table->float('duree');                        // en heures (ex: 1.5 = 1h30)
            $table->text('commentaire')->nullable();
            $table->boolean('facturable')->default(true);
            $table->timestamps();

            $table->foreign('IDTicket')->references('ID')->on('ticket')->onDelete('cascade');
            $table->foreign('IDUser')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_entries');
    }
};
