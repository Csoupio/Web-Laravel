<?php

use Illuminate\Database\Migrations\Migration;

// Cette migration est désormais inutile : la colonne 'role' est définie
// directement dans 0001_01_01_000000_create_users_table.php
return new class extends Migration
{
    public function up(): void
    {
        // vide — role intégré dans la migration initiale
    }

    public function down(): void
    {
        // vide
    }
};
