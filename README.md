# 🏥 IGFL - Système de Gestion Hospitalier

Application web Symfony pour la gestion des patients, dossiers médicaux et greffes de foie au sein de l'Institut de Greffe de Foie Limoges.

## 📋 Description

Plateforme hospitalière complète permettant :
- **Administrateurs** : Gestion centralisée des utilisateurs, patients, profils et permissions
- **Médecins** : Consultation des patients, gestion des dossiers, enregistrement des consultations et greffes
- **Patients** : Accès à leurs dossiers et historique médical

## 🚀 Démarrage Rapide

### Prérequis
- [Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
- Docker et Docker Desktop

### Installation et Lancement

```bash
# 1. Construire les images Docker
docker compose build --pull --no-cache

# 2. Lancer l'application
docker compose up --wait

# 3. Accéder à l'application
# Ouvrir https://localhost dans votre navigateur
# Accepter le certificat TLS auto-généré

# 4. Arrêter l'application
docker compose down --remove-orphans
```

## 🎨 Architecture

### Rôles et Permissions

| Rôle | Couleur | Accès |
|------|--------|-------|
| **Admin** 🔵 | Bleu | EasyAdmin - Gestion complète du système |
| **Médecin** 🟢 | Vert | Patients, Dossiers, Consultations, Greffes |
| **Patient** 🟠 | Orange | Consultation de son dossier, notes médicales |

### Stack Technique

- **Framework** : Symfony 7.0
- **Base de Données** : MySQL 8.0
- **Serveur Web** : FrankenPHP + Caddy
- **Template Engine** : Twig
- **Admin Panel** : EasyAdmin
- **CSS** : Bootstrap 5.3 + CSS personnalisé centralisé
- **Frontend** : Stimulus JS + Asset Mapper

### Structure du Projet

```
src/
├── Controller/          # Contrôleurs (Admin, Médecin, Patient)
├── Entity/              # Entités Doctrine (Utilisateur, Patient, Dossier, etc.)
├── Repository/          # Requêtes personnalisées
└── Security/            # Authentification et autorisations

templates/
├── admin/               # Interface administrateur (EasyAdmin)
├── medecin/             # Interface médecin (Patients, Dossiers)
└── home/medecin/        # Dashboard et Consultations

assets/styles/
├── layouts.css          # Navbars, layouts (color-coded)
├── cards.css            # Composants card
├── forms.css            # Formulaires
├── global.css           # Boutons, tables, badges
└── consultations.css    # Pages consultations
```

## 👤 Authentification

### Créer un Admin
```bash
docker compose exec php bin/console app:create-admin
```

### Accès Défaut
- **Admin** : https://localhost/admin
- **Médecin** : https://localhost/ (après authentification)
- **Patient** : https://localhost/ (après authentification)

## 📚 Fonctionnalités

### Pour l'Administrateur
- 👥 Gestion des Logins (email + mot de passe)
- 👤 Gestion des Utilisateurs (civiles, profils)
- 🏷️ Gestion des Profils (rôles et permissions)
- 🏥 Gestion des Patients
- 💊 Gestion des Greffes

### Pour le Médecin
- 👥 Liste et détails des patients
- 📁 Gestion des dossiers patient
- 📋 Enregistrement des consultations
- 💉 Suivi des greffes
- 📝 Notes médicales

### Pour le Patient
- 📄 Consultation de son dossier
- 📋 Historique des consultations
- 📊 État de la greffe

## 🎨 Styling

Tous les styles sont **centralisés** dans `assets/styles/` :
- ❌ Pas de `<style>` inline dans les templates
- ❌ Pas d'attributs `style=` dans le HTML
- ✅ Classe CSS génériques et réutilisables
- ✅ Système de couleurs contextuel (admin/médecin/patient)

### Classes Principales
```twig
<div class="admin">      <!-- Applique couleurs bleues -->
<div class="medecin">    <!-- Applique couleurs vertes -->
<div class="patient">    <!-- Applique couleurs oranges -->
```

## 🔧 Commandes Utiles

```bash
# Lancer les tests
docker compose exec php bin/phpunit

# Générer les migrations
docker compose exec php bin/console make:migration

# Exécuter les migrations
docker compose exec php bin/console doctrine:migrations:migrate

# Accéder au shell PHP
docker compose exec php bash

# Voir les logs
docker compose logs -f php
```

## 📖 Documentation Complémentaire

- [Guide EasyAdmin](aides/docs/easyadmin-guide.md)
- [Implémentation Médecin](aides/IMPLEMENTATION_MEDECIN.md)
- [Implémentation Dossier Patient](aides/IMPLEMENTATION_DOSSIER_PATIENT.md)
- [Documentation Greffe](aides/DOCUMENTATION_DOSSIER_GREFFE.md)
- [Checklist Médecin](aides/CHECKLIST_MEDECIN.md)

## 🛠️ Configuration

### Variables d'Environnement

Voir `.env.local` pour :
- `DATABASE_URL` : Connexion MySQL
- `MAILER_DSN` : Configuration email
- `APP_SECRET` : Clé secrète Symfony

### Docker Compose

- `compose.yaml` : Configuration de développement
- `compose.prod.yaml` : Configuration production
- `compose.override.yaml` : Surcharges locales

## 📝 Licence

Propriétaire - Institut de Greffe de Foie Limoges (IGFL)

## 👥 Équipe

Projet académique - BTS Informatique (2ème année)
