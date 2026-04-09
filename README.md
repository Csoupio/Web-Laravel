# Système de Gestion de Ticketing et Facturation

Une plateforme web complète et moderne pour la gestion de projets, le suivi du temps et la facturation client, développée avec le framework **Laravel**.

## 🚀 Fonctionnalités

### 👤 Espace Client
- **Dashboard Dynamique** : Visualisation globale des projets et tickets en cours.
- **Gestion des Tickets** : Création, consultation et suivi des bugs ou évolutions.
- **Collaboration** : Ajout de commentaires en temps réel sur les tickets.
- **Validation Budgétaire** : Soumission et approbation des temps passés pour facturation.

### 🛠️ Espace Collaborateur
- **Saisie de Temps** : Enregistrement précis des heures travaillées par ticket.
- **Suivi de Projet** : Accès aux détails des projets assignés.
- **Reporting** : Consultation des rapports de temps par projet.

### 👑 Espace Administrateur
- **Gestion des Utilisateurs** : Création et gestion des comptes Administrateurs, Collaborateurs et Clients.
- **Gestion des Projets** : Création de projets, assignation de ressources et définition des budgets/contrats.
- **Maintenance du Système** : Forçage de statut des tickets et gestion globale de la base de données.

## 🛠️ Stack Technique

- **Framework** : Laravel 11.x / 12.x
- **Frontend** : Blade, JavaScript (ES6+), Vanilla CSS
- **Base de données** : SQLite (standardisée en `snake_case`)
- **Middlewares** : Gestion personnalisée des rôles (`CheckRole`) et de l'auth (`CheckAuth`).

## ⚙️ Installation

### Prérequis
- **PHP** >= 8.2
- **Composer**
- **Node.js** & **NPM**

### Installation Rapide
Le projet inclut un script d'automatisation pour configurer l'environnement :

```bash
# 1. Configurer l'environnement (dépendances, base de données, seeding)
npm run setup

# 2. Lancer le serveur de développement et Vite
npm run dev
```

### Installation Manuelle
Si vous préférez installer le projet étape par étape :

1. **Clonage & Dépendances**
   ```bash
   composer install
   npm install
   ```

2. **Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Base de données**
   ```bash
   touch database/database.sqlite
   php artisan migrate:fresh --seed
   ```

4. **Lancement**
   ```bash
   php artisan serve
   npm run dev
   ```

## 🔑 Identifiants de test

Le système est livré avec un jeu de données complet via le `seed`. Voici les accès par défaut :

| Rôle | Email | Mot de passe |
| :--- | :--- | :--- |
| **Administrateur** | `alice@admin.com` | `admin123` |
| **Collaborateur** | `baptiste@agence.com` | `collab123` |
| **Client** | `francois@acme.com` | `client123` |

## 📁 Structure du Projet

- `app/Models/` : Modèles Eloquent standardisés (sans debt technique).
- `app/Http/Controllers/` : Logique métier propre et typée.
- `resources/views/` : Vues Blade organisées par modules (auth, client, admin, ticket).
- `public/css/` : Design system centralisé (home.css, auth.css, etc.).

---
*Projet réalisé dans le cadre d'un cours de développement web sur les standards de production Laravel.*
