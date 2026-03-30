<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users — utilise les colonnes de la migration Laravel par défaut
        DB::table('users')->insert([
            [
                'name'              => 'Admin',
                'email'             => 'admin@admin.com',
                'password'          => bcrypt('admin'),
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'name'              => 'Jean Dupont',
                'email'             => 'jean@acme.com',
                'password'          => bcrypt('1234'),
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ]);

        // Clients
        DB::table('clients')->insert([
            [
                'Nom'        => 'Acme Corp',
                'email'      => 'contact@acme.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nom'        => 'Tech Solutions',
                'email'      => 'info@techsol.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Projets
        DB::table('projets')->insert([
            [
                'Nom'         => 'Site vitrine',
                'ClientsID'   => 1,
                'Description' => 'Refonte du site web',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'Nom'         => 'App mobile',
                'ClientsID'   => 2,
                'Description' => 'Application iOS et Android',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);

        // Tickets
        DB::table('ticket')->insert([
            [
                'Nom'          => 'Bug header mobile',
                'IDProjet'     => 1,
                'Status'       => 'Ouvert',
                'Priorité'     => 'Haute',
                'Type'         => 'Bug',
                'Descritpion'  => 'Le header se superpose sur mobile.',
                'Temps_Estime' => 2,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'Nom'          => 'Page contact',
                'IDProjet'     => 1,
                'Status'       => 'En cours',
                'Priorité'     => 'Moyenne',
                'Type'         => 'Évolution',
                'Descritpion'  => 'Ajouter un formulaire de contact.',
                'Temps_Estime' => 4,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'Nom'          => 'Login Facebook',
                'IDProjet'     => 2,
                'Status'       => 'Ouvert',
                'Priorité'     => 'Basse',
                'Type'         => 'Évolution',
                'Descritpion'  => 'Intégrer la connexion Facebook.',
                'Temps_Estime' => 6,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}
