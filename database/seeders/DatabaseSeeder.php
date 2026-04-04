<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── USERS ─────────────────────────────────────────────────────────────

        DB::table('users')->insert([
            // Administrateurs
            [
                'name'       => 'Alice Martin',
                'email'      => 'alice@admin.com',
                'password'   => Hash::make('admin123'),
                'role'       => 'Administrateur',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Collaborateurs
            [
                'name'       => 'Baptiste Lefèvre',
                'email'      => 'baptiste@agence.com',
                'password'   => Hash::make('collab123'),
                'role'       => 'Collaborateur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Clara Dupuis',
                'email'      => 'clara@agence.com',
                'password'   => Hash::make('collab123'),
                'role'       => 'Collaborateur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Dorian Schmitt',
                'email'      => 'dorian@agence.com',
                'password'   => Hash::make('collab123'),
                'role'       => 'Collaborateur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Emma Rousseau',
                'email'      => 'emma@agence.com',
                'password'   => Hash::make('collab123'),
                'role'       => 'Collaborateur',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Clients
            [
                'name'       => 'François Bernard',
                'email'      => 'francois@acme.com',
                'password'   => Hash::make('client123'),
                'role'       => 'Client',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Gabrielle Morel',
                'email'      => 'gabrielle@techsol.com',
                'password'   => Hash::make('client123'),
                'role'       => 'Client',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Hugo Petit',
                'email'      => 'hugo@biolab.com',
                'password'   => Hash::make('client123'),
                'role'       => 'Client',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Inès Garnier',
                'email'      => 'ines@urbanstore.com',
                'password'   => Hash::make('client123'),
                'role'       => 'Client',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ── CLIENTS ───────────────────────────────────────────────────────────

        DB::table('clients')->insert([
            [
                'Nom'        => 'Acme Corp',
                'email'      => 'francois@acme.com',
                'user_id'    => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nom'        => 'Tech Solutions',
                'email'      => 'gabrielle@techsol.com',
                'user_id'    => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nom'        => 'BioLab Recherche',
                'email'      => 'hugo@biolab.com',
                'user_id'    => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nom'        => 'Urban Store',
                'email'      => 'ines@urbanstore.com',
                'user_id'    => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ── PROJETS ───────────────────────────────────────────────────────────

        DB::table('projets')->insert([
            [
                'Nom'         => 'Refonte site vitrine',
                'ClientsID'   => 1,
                'Description' => 'Refonte complète du site web corporate d\'Acme Corp avec nouveau design et CMS.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'Nom'         => 'Application mobile interne',
                'ClientsID'   => 1,
                'Description' => 'Application iOS/Android pour la gestion des stocks en entrepôt.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'Nom'         => 'Plateforme SaaS B2B',
                'ClientsID'   => 2,
                'Description' => 'Développement d\'une plateforme SaaS de gestion de projets pour PME.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'Nom'         => 'Intégration API partenaires',
                'ClientsID'   => 2,
                'Description' => 'Connexion des APIs tierces (Stripe, Salesforce, HubSpot) à la plateforme existante.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'Nom'         => 'Portail résultats patients',
                'ClientsID'   => 3,
                'Description' => 'Interface web sécurisée permettant aux patients de consulter leurs résultats d\'analyse.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'Nom'         => 'E-commerce Urban Store',
                'ClientsID'   => 4,
                'Description' => 'Boutique en ligne avec gestion des stocks, paiement et programme de fidélité.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'Nom'         => 'Dashboard analytics',
                'ClientsID'   => 4,
                'Description' => 'Tableau de bord de suivi des ventes, conversions et comportements utilisateurs.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);

        // ── ASSIGNATIONS COLLABORATEURS ────────────────────────────────────────

        DB::table('projet_user')->insert([
            ['projet_id' => 1, 'user_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['projet_id' => 1, 'user_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['projet_id' => 2, 'user_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['projet_id' => 2, 'user_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['projet_id' => 3, 'user_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['projet_id' => 3, 'user_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['projet_id' => 3, 'user_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['projet_id' => 4, 'user_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['projet_id' => 4, 'user_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['projet_id' => 5, 'user_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['projet_id' => 6, 'user_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['projet_id' => 6, 'user_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['projet_id' => 6, 'user_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['projet_id' => 7, 'user_id' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── TICKETS ───────────────────────────────────────────────────────────

        DB::table('ticket')->insert([

            // Projet 1 — Refonte site vitrine
            ['Nom' => 'Header qui se superpose sur mobile',       'IDProjet' => 1, 'Status' => 'Terminé',  'Priorité' => 'Haute',   'Type' => 'Bug',       'Descritpion' => 'Sur les écrans < 768px le menu de navigation recouvre le contenu principal. Reproduit sur Chrome et Safari mobile.', 'Temps_Estime' => 3,  'created_at' => now(), 'updated_at' => now()],
            ['Nom' => 'Intégration police Inter',                 'IDProjet' => 1, 'Status' => 'Terminé',  'Priorité' => 'Basse',   'Type' => 'Évolution', 'Descritpion' => 'Remplacer la police système par la police Inter (Google Fonts) sur l\'ensemble du site.',                         'Temps_Estime' => 1,  'created_at' => now(), 'updated_at' => now()],
            ['Nom' => 'Formulaire de contact non fonctionnel',    'IDProjet' => 1, 'Status' => 'En cours', 'Priorité' => 'Haute',   'Type' => 'Bug',       'Descritpion' => 'Le formulaire de contact ne renvoie pas de confirmation après envoi. Les emails ne sont pas reçus côté admin.', 'Temps_Estime' => 4,  'created_at' => now(), 'updated_at' => now()],
            ['Nom' => 'Ajouter section témoignages clients',      'IDProjet' => 1, 'Status' => 'Ouvert',   'Priorité' => 'Moyenne', 'Type' => 'Évolution', 'Descritpion' => 'Créer une section avec un slider de témoignages clients sur la page d\'accueil.',                               'Temps_Estime' => 5,  'created_at' => now(), 'updated_at' => now()],
            ['Nom' => 'Optimisation images (WebP)',               'IDProjet' => 1, 'Status' => 'Ouvert',   'Priorité' => 'Moyenne', 'Type' => 'Évolution', 'Descritpion' => 'Convertir toutes les images en format WebP pour améliorer le score Lighthouse.',                               'Temps_Estime' => 2,  'created_at' => now(), 'updated_at' => now()],

            // Projet 2 — Application mobile interne
            ['Nom' => 'Crash au scan de QR code',                 'IDProjet' => 2, 'Status' => 'Bloqué',   'Priorité' => 'Haute',   'Type' => 'Bug',       'Descritpion' => 'L\'application crash sur Android 12 lors du scan de QR code. iOS non impacté. En attente du SDK.',             'Temps_Estime' => 8,  'created_at' => now(), 'updated_at' => now()],
            ['Nom' => 'Mode hors-ligne',                          'IDProjet' => 2, 'Status' => 'En cours', 'Priorité' => 'Haute',   'Type' => 'Évolution', 'Descritpion' => 'Permettre la saisie d\'inventaire sans connexion internet avec synchronisation différée.',                      'Temps_Estime' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['Nom' => 'Notifications push alertes de stock',      'IDProjet' => 2, 'Status' => 'Ouvert',   'Priorité' => 'Moyenne', 'Type' => 'Évolution', 'Descritpion' => 'Envoyer une notification push quand un article passe sous le seuil de stock minimum défini.',                  'Temps_Estime' => 6,  'created_at' => now(), 'updated_at' => now()],

            // Projet 3 — Plateforme SaaS B2B
            ['Nom' => 'Erreur 500 création workspace',            'IDProjet' => 3, 'Status' => 'En cours', 'Priorité' => 'Haute',   'Type' => 'Bug',       'Descritpion' => 'La création d\'un workspace retourne une erreur 500 quand le nom contient des caractères spéciaux.',           'Temps_Estime' => 3,  'created_at' => now(), 'updated_at' => now()],
            ['Nom' => 'Système de permissions par rôle',          'IDProjet' => 3, 'Status' => 'En cours', 'Priorité' => 'Haute',   'Type' => 'Évolution', 'Descritpion' => 'Implémenter Admin, Manager, Membre, Invité avec droits granulaires sur chaque ressource.',                    'Temps_Estime' => 16, 'created_at' => now(), 'updated_at' => now()],
            ['Nom' => 'Export CSV des projets',                   'IDProjet' => 3, 'Status' => 'Ouvert',   'Priorité' => 'Moyenne', 'Type' => 'Évolution', 'Descritpion' => 'Ajouter un bouton d\'export CSV sur la liste des projets avec filtres appliqués.',                            'Temps_Estime' => 4,  'created_at' => now(), 'updated_at' => now()],
            ['Nom' => 'Ralentissement dashboard > 1000 projets',  'IDProjet' => 3, 'Status' => 'Ouvert',   'Priorité' => 'Haute',   'Type' => 'Bug',       'Descritpion' => 'Le dashboard devient très lent (> 8s) pour les comptes ayant plus de 1000 projets.',                          'Temps_Estime' => 10, 'created_at' => now(), 'updated_at' => now()],

            // Projet 4 — Intégration API partenaires
            ['Nom' => 'Webhook Stripe non reçu',                  'IDProjet' => 4, 'Status' => 'Terminé',  'Priorité' => 'Haute',   'Type' => 'Bug',       'Descritpion' => 'Les webhooks de paiement Stripe ne sont pas reçus en production. Commandes bloquées en attente.',             'Temps_Estime' => 5,  'created_at' => now(), 'updated_at' => now()],
            ['Nom' => 'Synchronisation contacts Salesforce',      'IDProjet' => 4, 'Status' => 'En cours', 'Priorité' => 'Moyenne', 'Type' => 'Évolution', 'Descritpion' => 'Synchroniser automatiquement les nouveaux contacts vers Salesforce toutes les heures.',                       'Temps_Estime' => 8,  'created_at' => now(), 'updated_at' => now()],
            ['Nom' => 'Intégration HubSpot suivi des leads',      'IDProjet' => 4, 'Status' => 'Ouvert',   'Priorité' => 'Basse',   'Type' => 'Évolution', 'Descritpion' => 'Envoyer les événements de conversion (inscription, upgrade) vers HubSpot pour le suivi marketing.',          'Temps_Estime' => 6,  'created_at' => now(), 'updated_at' => now()],

            // Projet 5 — Portail résultats patients
            ['Nom' => 'Authentification double facteur',          'IDProjet' => 5, 'Status' => 'En cours', 'Priorité' => 'Haute',   'Type' => 'Évolution', 'Descritpion' => 'Implémenter la 2FA par SMS et TOTP (Google Authenticator) pour sécuriser l\'accès.',                         'Temps_Estime' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['Nom' => 'PDF résultats illisible sur mobile',       'IDProjet' => 5, 'Status' => 'Ouvert',   'Priorité' => 'Moyenne', 'Type' => 'Bug',       'Descritpion' => 'Les PDF ne s\'affichent pas correctement dans le viewer intégré sur iOS Safari. Texte tronqué.',             'Temps_Estime' => 4,  'created_at' => now(), 'updated_at' => now()],
            ['Nom' => 'Historique des analyses sur 2 ans',        'IDProjet' => 5, 'Status' => 'Ouvert',   'Priorité' => 'Basse',   'Type' => 'Évolution', 'Descritpion' => 'Permettre aux patients de consulter leurs analyses sur 2 ans avec graphiques d\'évolution.',                 'Temps_Estime' => 14, 'created_at' => now(), 'updated_at' => now()],

            // Projet 6 — E-commerce Urban Store
            ['Nom' => 'Panier vidé après 30 min d\'inactivité',  'IDProjet' => 6, 'Status' => 'Terminé',  'Priorité' => 'Haute',   'Type' => 'Bug',       'Descritpion' => 'Le panier est vidé après 30 minutes même si l\'utilisateur est connecté. Session expire trop tôt.',           'Temps_Estime' => 3,  'created_at' => now(), 'updated_at' => now()],
            ['Nom' => 'Programme de fidélité — points',           'IDProjet' => 6, 'Status' => 'En cours', 'Priorité' => 'Moyenne', 'Type' => 'Évolution', 'Descritpion' => '1€ dépensé = 1 point, 100 points = 5€ de réduction. Inclure un historique des points.',                    'Temps_Estime' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['Nom' => 'Page produit : zoom image',                'IDProjet' => 6, 'Status' => 'Ouvert',   'Priorité' => 'Basse',   'Type' => 'Évolution', 'Descritpion' => 'Ajouter un zoom au survol sur les photos produit en page de détail.',                                        'Temps_Estime' => 2,  'created_at' => now(), 'updated_at' => now()],
            ['Nom' => 'Filtre taille/couleur non persistant',     'IDProjet' => 6, 'Status' => 'Ouvert',   'Priorité' => 'Moyenne', 'Type' => 'Bug',       'Descritpion' => 'Les filtres catalogue sont réinitialisés quand on navigue vers un produit puis revient en arrière.',          'Temps_Estime' => 3,  'created_at' => now(), 'updated_at' => now()],
            ['Nom' => 'Support paiement Apple Pay / Google Pay',  'IDProjet' => 6, 'Status' => 'Ouvert',   'Priorité' => 'Haute',   'Type' => 'Évolution', 'Descritpion' => 'Intégrer Apple Pay et Google Pay comme méthodes de paiement supplémentaires via Stripe.',                    'Temps_Estime' => 8,  'created_at' => now(), 'updated_at' => now()],

            // Projet 7 — Dashboard analytics
            ['Nom' => 'Graphique CA mensuel avec comparaison N-1','IDProjet' => 7, 'Status' => 'Terminé',  'Priorité' => 'Haute',   'Type' => 'Évolution', 'Descritpion' => 'Afficher un graphique linéaire du CA mensuel avec comparaison N-1 et indicateurs clés.',                    'Temps_Estime' => 6,  'created_at' => now(), 'updated_at' => now()],
            ['Nom' => 'Export rapport PDF',                       'IDProjet' => 7, 'Status' => 'En cours', 'Priorité' => 'Moyenne', 'Type' => 'Évolution', 'Descritpion' => 'Permettre l\'export du dashboard en PDF avec logo et mise en page professionnelle.',                         'Temps_Estime' => 7,  'created_at' => now(), 'updated_at' => now()],
            ['Nom' => 'Données incorrectes taux de conversion',   'IDProjet' => 7, 'Status' => 'Ouvert',   'Priorité' => 'Haute',   'Type' => 'Bug',       'Descritpion' => 'Le taux de conversion affiché (3.2%) ne correspond pas aux calculs manuels (1.8%). Problème dans l\'agrégation.', 'Temps_Estime' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['Nom' => 'Alerte si CA < objectif hebdomadaire',     'IDProjet' => 7, 'Status' => 'Ouvert',   'Priorité' => 'Basse',   'Type' => 'Évolution', 'Descritpion' => 'Envoyer un email automatique au manager si le CA hebdomadaire est inférieur à l\'objectif défini.',          'Temps_Estime' => 4,  'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
