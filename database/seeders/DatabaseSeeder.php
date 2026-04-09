<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Projet;
use App\Models\Ticket;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── USERS ─────────────────────────────────────────────────────────────
        $admin = User::create([
            'name'     => 'Alice Martin',
            'email'    => 'alice@admin.com',
            'password' => 'admin123',
            'role'     => 'Administrateur',
        ]);

        $collab1 = User::create(['name' => 'Baptiste Lefèvre', 'email' => 'baptiste@agence.com', 'password' => 'collab123', 'role' => 'Collaborateur']);
        $collab2 = User::create(['name' => 'Clara Dupuis', 'email' => 'clara@agence.com', 'password' => 'collab123', 'role' => 'Collaborateur']);
        $collab3 = User::create(['name' => 'Dorian Schmitt', 'email' => 'dorian@agence.com', 'password' => 'collab123', 'role' => 'Collaborateur']);
        $collab4 = User::create(['name' => 'Emma Rousseau', 'email' => 'emma@agence.com', 'password' => 'collab123', 'role' => 'Collaborateur']);

        $clientUser1 = User::create(['name' => 'François Bernard', 'email' => 'francois@acme.com', 'password' => 'client123', 'role' => 'Client']);
        $clientUser2 = User::create(['name' => 'Gabrielle Morel', 'email' => 'gabrielle@techsol.com', 'password' => 'client123', 'role' => 'Client']);
        $clientUser3 = User::create(['name' => 'Hugo Petit', 'email' => 'hugo@biolab.com', 'password' => 'client123', 'role' => 'Client']);
        $clientUser4 = User::create(['name' => 'Inès Garnier', 'email' => 'ines@urbanstore.com', 'password' => 'client123', 'role' => 'Client']);

        // ── CLIENTS ───────────────────────────────────────────────────────────
        $client1 = Client::create(['nom' => 'Acme Corp', 'email' => 'francois@acme.com', 'user_id' => $clientUser1->id]);
        $client2 = Client::create(['nom' => 'Tech Solutions', 'email' => 'gabrielle@techsol.com', 'user_id' => $clientUser2->id]);
        $client3 = Client::create(['nom' => 'BioLab Recherche', 'email' => 'hugo@biolab.com', 'user_id' => $clientUser3->id]);
        $client4 = Client::create(['nom' => 'Urban Store', 'email' => 'ines@urbanstore.com', 'user_id' => $clientUser4->id]);

        // ── PROJETS ───────────────────────────────────────────────────────────
        $p1 = Projet::create(['nom' => 'Refonte site vitrine', 'client_id' => $client1->id, 'description' => 'Refonte complète du site web corporate d\'Acme Corp avec nouveau design et CMS.']);
        $p2 = Projet::create(['nom' => 'Application mobile interne', 'client_id' => $client1->id, 'description' => 'Application iOS/Android pour la gestion des stocks en entrepôt.']);
        $p3 = Projet::create(['nom' => 'Plateforme SaaS B2B', 'client_id' => $client2->id, 'description' => 'Développement d\'une plateforme SaaS de gestion de projets pour PME.']);
        $p4 = Projet::create(['nom' => 'Intégration API partenaires', 'client_id' => $client2->id, 'description' => 'Connexion des APIs tierces (Stripe, Salesforce, HubSpot) à la plateforme existante.']);
        $p5 = Projet::create(['nom' => 'Portail résultats patients', 'client_id' => $client3->id, 'description' => 'Interface web sécurisée permettant aux patients de consulter leurs résultats d\'analyse.']);
        $p6 = Projet::create(['nom' => 'E-commerce Urban Store', 'client_id' => $client4->id, 'description' => 'Boutique en ligne avec gestion des stocks, paiement et programme de fidélité.']);
        $p7 = Projet::create(['nom' => 'Dashboard analytics', 'client_id' => $client4->id, 'description' => 'Tableau de bord de suivi des ventes, conversions et comportements utilisateurs.']);

        // ── ASSIGNATIONS ──────────────────────────────────────────────────────
        $p1->collaborateurs()->attach([$collab1->id, $collab2->id]);
        $p2->collaborateurs()->attach([$collab3->id, $collab4->id]);
        $p3->collaborateurs()->attach([$collab1->id, $collab3->id, $collab4->id]);
        $p4->collaborateurs()->attach([$collab2->id, $collab3->id]);
        $p5->collaborateurs()->attach([$collab4->id]);
        $p6->collaborateurs()->attach([$collab1->id, $collab2->id, $collab4->id]);
        $p7->collaborateurs()->attach([$collab3->id]);

        // ── TICKETS ───────────────────────────────────────────────────────────
        Ticket::create(['nom' => 'Header qui se superpose sur mobile', 'projet_id' => $p1->id, 'statut' => 'Terminé', 'priorite' => 'Haute', 'type' => 'Bug', 'description' => 'Sur les écrans < 768px le menu de navigation recouvre le contenu principal.', 'temps_estime' => 3]);
        Ticket::create(['nom' => 'Intégration police Inter', 'projet_id' => $p1->id, 'statut' => 'Terminé', 'priorite' => 'Basse', 'type' => 'Évolution', 'description' => 'Remplacer la police système par la police Inter.', 'temps_estime' => 1]);
        Ticket::create(['nom' => 'Formulaire de contact non fonctionnel', 'projet_id' => $p1->id, 'statut' => 'En cours', 'priorite' => 'Haute', 'type' => 'Bug', 'description' => 'Le formulaire de contact ne renvoie pas de confirmation après envoi.', 'temps_estime' => 4]);
        
        Ticket::create(['nom' => 'Crash au scan de QR code', 'projet_id' => $p2->id, 'statut' => 'Bloqué', 'priorite' => 'Haute', 'type' => 'Bug', 'description' => 'L\'application crash sur Android 12 lors du scan de QR code.', 'temps_estime' => 8]);
        Ticket::create(['nom' => 'Mode hors-ligne', 'projet_id' => $p2->id, 'statut' => 'En cours', 'priorite' => 'Haute', 'type' => 'Évolution', 'description' => 'Saisie d\'inventaire sans connexion.', 'temps_estime' => 12]);
        
        Ticket::create(['nom' => 'Erreur 500 création workspace', 'projet_id' => $p3->id, 'statut' => 'En cours', 'priorite' => 'Haute', 'type' => 'Bug', 'description' => 'Erreur 500 quand le nom contient des caractères spéciaux.', 'temps_estime' => 3]);
        Ticket::create(['nom' => 'Système de permissions par rôle', 'projet_id' => $p3->id, 'statut' => 'En cours', 'priorite' => 'Haute', 'type' => 'Évolution', 'description' => 'Admin, Manager, Membre avec droits granulaires.', 'temps_estime' => 16]);
    }
}
