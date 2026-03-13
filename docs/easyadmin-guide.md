# Guide EasyAdmin - Institut de Greffe de Foie Limoges

## ✅ STATUS : ENTIÈREMENT CONFIGURÉ ET PRÊT À L'EMPLOI

Tous les CRUD controllers et la configuration EasyAdmin sont maintenant complètement en place. Vous pouvez accéder à votre interface d'administration à : **`http://localhost:8000/admin`**

---

## Table des matières
1. [Introduction](#introduction)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Création des CRUD](#création-des-crud)
5. [Dashboard](#dashboard)
6. [Sécurité](#sécurité)
7. [Questions Fréquentes](#questions-fréquentes)

---

## Introduction

**EasyAdmin** est un bundle Symfony qui permet de créer rapidement une interface d'administration (admin backend) professionnelle pour votre application. C'est un outil puissant et flexible qui permet de gérer vos entités Doctrine sans avoir à écrire de HTML/CSS complexe.

### Avantages d'EasyAdmin

- 🚀 **Rapide** : Créez une interface admin en quelques minutes
- 🎨 **Beau** : Interface moderne et responsive (Bootstrap 5)
- 📱 **Mobile-friendly** : Fonctionne parfaitement sur mobile/tablette
- 🔍 **Recherche & Filtres** : Intégrés par défaut
- 🔐 **Sécurité** : Contrôle d'accès et permissions inclus
- 📊 **CRUD complet** : Create, Read, Update, Delete automatique
- 🧩 **Extensible** : Personnalisable avec Twig/CSS/JS

### Requirements

- PHP 8.1+
- Symfony 5.4+
- Doctrine ORM

---

## Installation

### Étape 1 : Installer le bundle via Composer

```bash
composer require easycorp/easyadmin-bundle
```

**Note** : Avec Symfony Flex, la configuration automatique se fait.

### Étape 2 : Vérifier les bundles (si nécessaire)

Vérifiez que dans `config/bundles.php` vous avez :

```php
return [
    // ...
    EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle::class => ['all' => true],
    Symfony\UX\TwigComponent\TwigComponentBundle::class => ['all' => true],
];
```

### Étape 3 : Configuration de Twig Components (si nécessaire)

Créez/vérifiez le fichier `config/packages/twig_component.yaml` :

```yaml
twig_component:
    anonymous_template_directory: 'components/'
    defaults:
        # Namespace & directory for components
        App\Twig\Components\: 'components/'
```

---

## Configuration du Routage

### Qu'est-ce que le routage ?

Le fichier `config/routes.yaml` dit à Symfony quelles URLs correspondent à quels controllers. EasyAdmin a ses propres routes (URLs) qu'il faut ajouter dans ce fichier.

### Comment faire ?

**Fichier** : `config/routes.yaml`

Ouvrez ce fichier et ajoutez cette section après `controllers` :

```yaml
admin:
    resource: routes/easyadmin.yaml
    prefix: /admin
```

### Explication ligne par ligne

```yaml
admin:                          # Nom du groupe de routes (peut être n'importe quoi)
    resource: routes/easyadmin.yaml  # Charger les routes depuis le fichier easyadmin.yaml
    prefix: /admin              # Toutes les routes commenceront par /admin
```

### Résultat

Après cette configuration, EasyAdmin sera accessible à :
- `http://localhost:8000/admin` → page d'accueil admin
- `http://localhost:8000/admin/utilisateurs` → lister les utilisateurs
- `http://localhost:8000/admin/utilisateurs/new` → créer un utilisateur
- etc.

### Format du fichier routes.yaml (exemple complet)

```yaml
# yaml-language-server: $schema=../vendor/symfony/routing/Loader/schema/routing.schema.json

controllers:
    resource:
        path: ../src/Controller/
        namespace: App\Controller
    type: attribute

admin:
    resource: routes/easyadmin.yaml
    prefix: /admin

app_security:
    resource: routes/security.yaml

framework:
    resource: routes/framework.yaml

web_profiler:
    resource: routes/web_profiler.yaml
```

### Installation initiale

Pour installer EasyAdmin pour la première fois :

```bash
# Via Docker Compose
docker compose exec php composer require easycorp/easyadmin-bundle

# Puis vider le cache
docker compose exec php php bin/console cache:clear
```

---

## Création du Dashboard

Le Dashboard est le point d'entrée de l'interface admin. C'est ici qu'on configure le menu principal.

### Créer le DashboardController

**Fichier** : `src/Controller/Admin/DashboardController.php`

```php
<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use App\Entity\DossierPatient;
use App\Entity\Greffe;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Institut de Greffe - Admin')
            ->setFaviconPath('images/favicon.svg');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', Utilisateur::class);
        yield MenuItem::linkToCrud('Patients', 'fas fa-hospital-user', DossierPatient::class);
        yield MenuItem::linkToCrud('Greffes', 'fas fa-pills', Greffe::class);
    }
}
```

### Expliquer les méthodes

| Méthode | Rôle |
|---------|------|
| `configureDashboard()` | Définit le titre et option du dashboard |
| `configureMenuItems()` | Crée les liens du menu de navigation |
| `linkToDashboard()` | Lien vers la page d'accueil du dashboard |
| `linkToCrud()` | Lien vers un CRUD controller pour gérer une entité |

### Créer le template du Dashboard

Le controller appelle `return $this->render('admin/dashboard.html.twig');` donc il faut créer ce fichier.

**Fichier** : `templates/admin/dashboard.html.twig`

```twig
{% extends '@EasyAdmin/layout.html.twig' %}

{% block content %}
    <div class="container-xl">
        <div class="page-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Tableau de bord Admin</h1>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">
                    <section class="content">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info">
                                        <i class="fas fa-users"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Utilisateurs</span>
                                        <span class="info-box-number">Gérer les comptes</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success">
                                        <i class="fas fa-hospital-user"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Patients</span>
                                        <span class="info-box-number">Gérer les dossiers</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning">
                                        <i class="fas fa-pills"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Greffes</span>
                                        <span class="info-box-number">Gérer les transplantations</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
{% endblock %}
```

### Explication du template

- `{% extends '@EasyAdmin/layout.html.twig' %}` → Hérite du layout d'EasyAdmin (menu, header, etc.)
- `{% block content %}` → Zone de contenu personnalisée
- Les classes `info-box`, `info-box-icon`, `info-box-content` viennent du thème EasyAdmin

---

## Création des CRUD Controllers

Un **CRUD Controller** gère les opérations Create, Read, Update, Delete pour une entité.

### ✅ État actuel : CRUD controllers déjà créés

Les 3 CRUD controllers suivants ont déjà été créés et configurés :

1. **`src/Controller/Admin/LoginCrudController.php`** ✅
   - Gère la création/édition des Logins (Email + Password)

2. **`src/Controller/Admin/UtilisateurCrudController.php`** ✅
   - Gère la création/édition des Utilisateurs
   - Formulaire en 3 sections : Infos civiles, Authentification (Login), Profils

3. **`src/Controller/Admin/ProfilCrudController.php`** ✅
   - Gère la création/édition des Profils individuels

### ✅ Dashboard mis à jour

Le `DashboardController` affiche maintenant 5 menus :
- Tableau de bord
- **Logins** (fas fa-key) ← Créer les comptes d'accès
- **Utilisateurs** (fas fa-users) ← Créer les utilisateurs complets
- **Profils** (fas fa-user-tag) ← Gérer les rôles
- **Patients** (fas fa-hospital-user) ← Gérer les dossiers patients
- **Greffes** (fas fa-pills) ← Gérer les transplantations

### Générer un CRUD avec la commande make (si vous en avez besoin pour d'autres entités)

```bash
docker compose exec php php bin/console make:admin:crud
```

Cette commande vous demande :
1. **Quel entity voulez-vous créer un CRUD pour ?** → Choisissez une entité

Ensuite, elle génère automatiquement le CRUD controller pour cette entité.

### Créer plusieurs CRUD controllers

Vous pouvez lancer la commande **plusieurs fois** pour créer des CRUD pour chaque entité.

### Exemple : CRUD des Utilisateurs (avec Login et Profils) - DÉJÀ CRÉÉ

**Fichier** : `src/Controller/Admin/UtilisateurCrudController.php`

Vous avez une relation imbriquée (Login → Utilisateur → Profils). Voici la structure :

```php
<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use App\Entity\Login;
use App\Entity\Profil;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class UtilisateurCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Utilisateur::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // === SECTION 1 : Données civiles ===
            FormField::addPanel('Informations Civiles'),
            IdField::new('id_utilisateur')->hideOnForm(),
            TextField::new('Nom'),
            TextField::new('Prenom'),
            TextField::new('Ville_res', 'Ville'),
            TextField::new('CP', 'Code Postal'),

            // === SECTION 2 : Authentification (Login) ===
            FormField::addPanel('Authentification'),
            AssociationField::new('login')
                ->setRequired(true)
                ->setHelp('Sélectionnez un compte Login'),

            // === SECTION 3 : Profils/Rôles ===
            FormField::addPanel('Profils et Rôles'),
            CollectionField::new('profils')
                ->useEntriesForm()
                ->setEntryIsComplex(true)
                ->setHelp('Ajoutez les rôles de cet utilisateur. Cliquez "Ajouter" pour en créer un nouveau'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gestion des Utilisateurs')
            ->setPageTitle('new', 'Créer un utilisateur')
            ->setPageTitle('edit', 'Modifier un utilisateur')
            ->setPaginationPageSize(25)
            ->setDefaultSort(['id_utilisateur' => 'DESC']);
    }
}
```

### Créer le CRUD des Logins - DÉJÀ CRÉÉ

**Fichier** : `src/Controller/Admin/LoginCrudController.php`

```php
<?php

namespace App\Controller\Admin;

use App\Entity\Login;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class LoginCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Login::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id_login')->hideOnForm(),
            EmailField::new('Mail', 'Email'),
            TextField::new('Password', 'Mot de passe')
                ->hideOnIndex(),  // Ne pas montrer en liste
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gestion des Logins')
            ->setPageTitle('new', 'Créer un Login')
            ->setPageTitle('edit', 'Modifier un Login');
    }
}
```

### Créer le CRUD des Profils - DÉJÀ CRÉÉ

**Fichier** : `src/Controller/Admin/ProfilCrudController.php`

```php
<?php

namespace App\Controller\Admin;

use App\Entity\Profil;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class ProfilCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Profil::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id_profil')->hideOnForm(),
            TextField::new('Role'),
            AssociationField::new('utilisateur')
                ->setRequired(true),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gestion des Profils')
            ->setPageTitle('new', 'Assigner un Profil')
            ->setPageTitle('edit', 'Modifier un Profil');
    }
}
```

### Résumé du workflow de création complet

### ✅ CONFIGURATION COMPLÈTE - PRÊT À L'EMPLOI

L'interface d'administration EasyAdmin est maintenant **entièrement configurée** et prête à l'emploi !

**Accédez à votre admin panel** : `http://localhost:8000/admin`

Vous verrez 5 liens de menu :
1. Tableau de bord
2. Logins
3. Utilisateurs
4. Profils
5. Patients
6. Greffes

---

**ÉTAPE 1️⃣ : Créer les Logins (comptes d'accès)**

1. Allez au menu `Logins`
2. Cliquez `+ Créer` 
3. Remplissez :
   - **Email** : l'adresse de connexion (ex: jean.dupont@hopital.fr)
   - **Mot de passe** : le password d'accès
4. Validez

```
Résultat = 1 Login créé (identifiants de connexion)
```

---

**ÉTAPE 2️⃣ : Créer les Utilisateurs (FAÇON RECOMMANDÉE)**

1. Allez au menu `Utilisateurs`
2. Cliquez `+ Créer`
3. **Remplissez la Section 1 (Informations Civiles)** :
   - Nom : Dupont
   - Prénom : Jean
   - Ville : Limoges
   - Code Postal : 87000

4. **Remplissez la Section 2 (Authentification)** :
   - Cliquez le dropdown `Login`
   - Sélectionnez le Login créé à l'étape 1 (ex: jean.dupont@hopital.fr)

5. **Remplissez la Section 3 (Profils et Rôles)** :
   - Cliquez le bouton `+ Ajouter` dans la zone Profils
   - Un formulaire s'ouvre → remplissez le `Role` (ex: "Médecin")
   - Cliquez "Ajouter" pour confirmer ce profil
   - **Vous pouvez cliquer `+ Ajouter` plusieurs fois** pour ajouter d'autres rôles à cet utilisateur

6. Validez le formulaire entier

```
Résultat = Créé simultanément :
  - 1 Utilisateur (Dupont Jean, Limoges, 87000)
  - Lié au Login créé à l'étape 1
  - Avec 1 ou plusieurs Profils/Rôles assignés
```

---

**ÉTAPE 3️⃣ : Les Profils (2 façons de faire)**

**Façon A : Recommandée (via l'étape 2)**
- Vous avez déjà ajouté les Profils lors de la création de l'Utilisateur
- ✅ C'est la meilleure façon : tout est fait en 1 formulaire

**Façon B : Alternative (si vous avez besoin d'ajouter un profil à un utilisateur existant)**
1. Allez au menu `Profils`
2. Cliquez `+ Créer`
3. Remplissez :
   - **Role** : ex "Chirurgien"
   - **Utilisateur** : sélectionnez l'utilisateur qui doit avoir ce rôle (ex: Dupont Jean)
4. Validez

```
Quand utiliser la Façon B ? 
  → Que si vous avez oublié d'ajouter un profil à un utilisateur
  → Ou si vous voulez ajouter après coup un nouveau rôle à quelqu'un
  → Sinon, ça alourdit le workflow
```

---

### Tableau résumé : Qui fait quoi ?

| Étape | Menu | Action | Crée |
|-------|------|--------|------|
| 1️⃣ | Logins | Créer Login (email + password) | 1 Login |
| 2️⃣ | Utilisateurs | Créer Utilisateur + sélectionner Login + ajouter Profils | 1 Utilisateur + lié à 1 Login + 1+ Profils |
| 3️⃣ | Profils | ⚠️ OPTIONNEL - Ajouter profil à utilisateur existant | 1 Profil supplémentaire |

---

### Exemple concret

**Créer : Jean Dupont, chirurgien et responsable médical**

✅ **Façon simple (recommandée)** :
1. Menu Logins → Créer : `email: jean@hopital.fr`, `password: xxx`
2. Menu Utilisateurs → Créer :
   - Nom: Dupont, Prénom: Jean, Ville: Limoges, CP: 87000
   - Login: sélectionner jean@hopital.fr
   - **Profils** → Cliquer `+ Ajouter` → Role: "Chirurgien" → Valider
   - **Profils** → Cliquer `+ Ajouter` → Role: "Responsable Médical" → Valider
3. Valider le formulaire Utilisateur

✗ **Façon compliquée (à éviter)** :
1. Menu Logins → Créer Login
2. Menu Profils → Créer Profil "Chirurgien"
3. Menu Profils → Créer Profil "Responsable Médical"
4. Menu Utilisateurs → Créer Utilisateur
5. Menu Utilisateurs → Éditer Utilisateur → Ajouter les Profils manuellement

**→ La première façon est bien meilleure !**

### Troubleshooting

**Problème** : Le formulaire ne montre pas l'option "créer un Login" en sélectionnant ?
**Solution** : Les AssociationFields dans EasyAdmin 5.x affichent une liste déroulante. Pour créer des Logins depuis le formulaire Utilisateur, allez d'abord créer les Logins dans le CRUD Login.

**Problème** : Les Profils ne s'ajoutent pas ?
**Solution** : Assurez-vous que la relation `profils` est bien définie comme `OneToMany` dans l'entité Utilisateur (c'est le cas dans votre structure).

---

## Types de champs disponibles

EasyAdmin propose de nombreux types de champs :

| Champ | Description | Exemple |
|-------|-------------|---------|
| `IdField` | Identifiant (caché en formulaire) | `IdField::new('id')` |
| `TextField` | Texte simple | `TextField::new('nom')` |
| `EmailField` | Email | `EmailField::new('email')` |
| `PasswordField` | Mot de passe (masqué) | `PasswordField::new('password')` |
| `TextEditorField` | Éditeur WYSIWYG | `TextEditorField::new('description')` |
| `DateField` | Date | `DateField::new('dateNaissance')` |
| `DateTimeField` | Date et heure | `DateTimeField::new('createdAt')` |
| `BooleanField` | Booléen (checkbox) | `BooleanField::new('actif')` |
| `IntegerField` | Nombre entier | `IntegerField::new('age')` |
| `MoneyField` | Montant en euros | `MoneyField::new('prix')` |
| `ChoiceField` | Liste déroulante | `ChoiceField::new('statut')->setChoices([])` |
| `ArrayField` | Tableau/Liste | `ArrayField::new('tags')` |
| `ImageField` | Upload d'image | `ImageField::new('photo')` |
| `FileField` | Upload de fichier | `FileField::new('document')` |
| `AssociationField` | Relation avec autre entité | `AssociationField::new('medecin')` |
| `CountryField` | Pays | `CountryField::new('pays')` |
| `PercentField` | Pourcentage | `PercentField::new('taux')` |
| `ColorField` | Sélecteur de couleur | `ColorField::new('couleur')` |

---

## Configurer les champs selon la page

Vous pouvez afficher différents champs selon qu'on est en création, édition ou consultation :

```php
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

public function configureFields(string $pageName): iterable
{
    if ($pageName === Crud::PAGE_NEW) {
        // Champs pour création (sans ID)
        return [
            TextField::new('login'),
            EmailField::new('email'),
            PasswordField::new('password'),
            ChoiceField::new('role')->setChoices([...]),
        ];
    } elseif ($pageName === Crud::PAGE_EDIT) {
        // Champs pour édition
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('login'),
            EmailField::new('email'),
            ChoiceField::new('role')->setChoices([...]),
        ];
    } elseif ($pageName === Crud::PAGE_DETAIL) {
        // Champs pour visualisation détaillée
        return [
            IdField::new('id'),
            TextField::new('login'),
            EmailField::new('email'),
            TextField::new('nom'),
            TextField::new('prenom'),
            ChoiceField::new('role')->setChoices([...]),
            DateTimeField::new('createdAt'),
        ];
    }

    return [];
}
```

Les constantes sont dans `EasyCorp\Bundle\EasyAdminBundle\Config\Crud` :
- `Crud::PAGE_INDEX` → Liste
- `Crud::PAGE_NEW` → Formulaire création
- `Crud::PAGE_EDIT` → Formulaire édition
- `Crud::PAGE_DETAIL` → Détail d'un élément

---

## Configurer les Actions (boutons)

Vous pouvez personnaliser quels boutons sont visibles :

```php
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

public function configureActions(Actions $actions): Actions
{
    return $actions
        // Pages et actions visibles
        ->add(Crud::PAGE_INDEX, Action::DETAIL)    // Voir détails
        ->add(Crud::PAGE_INDEX, Action::EDIT)      // Éditer
        ->add(Crud::PAGE_INDEX, Action::DELETE)    // Supprimer
        ->add(Crud::PAGE_DETAIL, Action::EDIT)     // Éditer depuis détail
        ->add(Crud::PAGE_DETAIL, Action::DELETE)   // Supprimer depuis détail
        // Exemple : désactiver des actions
        ->disable(Action::NEW)  // Ne pas créer de nouveaux
        ->disable(Action::DELETE);  // Ne pas supprimer
}
```

Actions disponibles :
- `Action::NEW` → Créer nouveau
- `Action::EDIT` → Éditer
- `Action::DELETE` → Supprimer
- `Action::DETAIL` → Voir détails
- `Action::BATCH_DELETE` → Supprimer plusieurs à la fois

---

## Configuration de la Sécurité

### Restreindre l'accès au Dashboard

Dans `config/packages/security.yaml` :

```yaml
security:
    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }  # Seulement admin
        - { path: ^/login, roles: PUBLIC_ACCESS }  # Public
```

### Restreindre par CRUD Controller

Dans votre controller, vérifiez les permissions :

```php
public function index(AdminContext $context): Response
{
    // Vérifier le rôle
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Accès refusé');
    
    return parent::index($context);
}

// Ou utiliser l'attribut
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
public function edit(AdminContext $context): Response
{
    return parent::edit($context);
}
```

---

## Ajouter des Filtres

Les filtres permettent aux admins de chercher/filtrer les données :

```php
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

public function configureFilters(Filters $filters): Filters
{
    return $filters
        ->add(TextFilter::new('login'))       // Chercher par login
        ->add(TextFilter::new('email'))       // Chercher par email
        ->add(DateFilter::new('createdAt'))   // Filtrer par date
        ->add(ChoiceFilter::new('role')       // Filtrer par rôle
            ->setChoices([
                'Administrateur' => 'ROLE_ADMIN',
                'Médecin' => 'ROLE_MEDECIN',
            ]));
}
```

---

## Personnaliser l'ordre des colonnes et la pagination

```php
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

public function configureCrud(Crud $crud): Crud
{
    return $crud
        ->setPageTitle('index', 'Liste des Utilisateurs')
        ->setPageTitle('new', 'Créer un utilisateur')
        ->setPageTitle('edit', 'Éditer l\'utilisateur')
        ->setPaginationPageSize(25)  // 25 résultats par page
        ->setDefaultSort(['id' => 'DESC']);  // Tri par défaut
}
```

---

## Convertir le h3 en lien vers EasyAdmin

Pour transformer un titre `<h3>Gestion des Utilisateurs</h3>` en bouton lien :

### Dans votre template Twig

Avant :
```twig
<h3>Gestion des Utilisateurs</h3>
<button class="admin-card-btn" disabled>Bientôt disponible</button>
```

Après :
```twig
<a href="{{ path('admin_utilisateur_index') }}" class="admin-card-link">
    <h3>Gestion des Utilisateurs</h3>
</a>
<a href="{{ path('admin_utilisateur_index') }}" class="admin-card-btn">Accéder</a>
```

### Nommer les routes EasyAdmin

EasyAdmin génère automatiquement les routes avec ce pattern :
- `admin_[entity]_index` → Liste des entités
- `admin_[entity]_new` → Créer une entité
- `admin_[entity]_edit` → Éditer une entité
- `admin_[entity]_detail` → Voir détails d'une entité
- `admin_[entity]_delete` → Supprimer une entité

Exemples pour votre projet :
- `admin_utilisateur_index` → `/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CUtilisateurCrudController`
- `admin_dossierpatient_index` → Patients
- `admin_greffe_index` → Greffes

---

## Questions Fréquentes

### Q1 : Je crée un CRUD mais je ne vois rien dans le menu

**Réponse** : Assurez-vous que le `DashboardController::configureMenuItems()` inclut le CRUD :

```php
yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', Utilisateur::class);
```

Le **premier paramètre** = titre au menu
Le **deuxième** = icône Font Awesome
Le **troisième** = classe de l'entité

### Q2 : Comment valider les données avant la sauvegarde ?

**Réponse** : Utilisez les assertions Symfony dans votre entité :

```php
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Column]
#[Assert\NotBlank(message: 'L\'email ne peut pas être vide')]
#[Assert\Email(message: 'L\'email doit être valide')]
private string $email;

#[ORM\Column]
#[Assert\Length(min: 3, minMessage: 'Le login doit faire au moins 3 caractères')]
private string $login;
```

### Q3 : Puis-je ajouter des actions personnalisées (boutons) ?

**Réponse** : Oui, créez une classe `Action` personnalisée :

```php
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

public function configureActions(Actions $actions): Actions
{
    $exportAction = Action::new('export', 'Exporter')
        ->linkToCrudAction('export')
        ->addCssClass('btn btn-primary');
    
    return $actions->add(Crud::PAGE_INDEX, $exportAction);
}

public function export(AdminContext $context): Response
{
    // Votre logique d'export
}
```

### Q4 : Comment récupérer l'utilisateur connecté dans un CRUD ?

**Réponse** : Injectez `Security` via le constructeur :

```php
use Symfony\Bundle\SecurityBundle\Security;

public function __construct(private Security $security)
{
}

public function persistEntity(EntityManagerInterface $em, $entityInstance): void
{
    $user = $this->security->getUser();
    $entityInstance->setCreatedBy($user);
    
    parent::persistEntity($em, $entityInstance);
}
```

### Q5 : Comment personnaliser les templates EasyAdmin ?

**Réponse** : Créez des templates Twig dans `templates/bundles/EasyAdminBundle/` :

```twig
{# templates/bundles/EasyAdminBundle/crud/index.html.twig #}
{% extends '@EasyAdmin/crud/index.html.twig' %}

{% block table_body %}
    {# Votre HTML personnalisé #}
{% endblock %}
```

### Q6 : Peut-on avoir plusieurs admins avec des permissions différentes ?

**Réponse** : Oui, avec les voters Symfony :

```php
#[IsGranted('ROLE_SUPER_ADMIN')]  // Seulement super admin
public function delete(AdminContext $context): Response
{
    return parent::delete($context);
}
```

---

## 🔧 CORRECTIONS ET BEST PRACTICES - EasyAdmin v5

Cette section documente tous les problèmes rencontrés et les solutions appliquées pour une intégration correcte d'EasyAdmin v5.

### ❌ ERREURS COURANTES ET SOLUTIONS

#### 1. Conflit de Routes - Route `/admin` Non Trouvée

**Problème** : `No route found for "GET /admin"`

**Cause** : Plusieurs contrôleurs définissent des routes sur `/admin`

**Solution** :
```php
// ❌ MAUVAIS - Ne pas faire
#[IsGranted('ROLE_ADMIN')]
#[Route('/admin', name: 'admin_dashboard')]
class AdminController extends AbstractController {
    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(): Response { ... }
}

// ✅ BON - Laisser EasyAdmin gérer la route /admin
#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController { ... }
```

**Action** : Supprimer tout route `/admin` des contrôleurs Symfony normaux.

---

#### 2. Menu Items - `linkToCrud()` N'existe Pas

**Problème** : `Call to undefined method linkToCrud()`

**Cause** : EasyAdmin v5 ne contient pas la méthode `linkToCrud()`

**Solution Correcte** :
```php
// ❌ MAUVAIS
yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', Utilisateur::class);

// ✅ BON - Utiliser linkToRoute() avec les noms de routes générées
yield MenuItem::linkToRoute('Utilisateurs', 'fas fa-users', 'admin_utilisateur_index');
yield MenuItem::linkToRoute('Logins', 'fas fa-key', 'admin_login_index');
yield MenuItem::linkToRoute('Profils', 'fas fa-user-tag', 'admin_profil_index');
```

**Pattern de Noms de Routes Générées** :
- `admin_{entityName}_index` → Lister
- `admin_{entityName}_new` → Créer
- `admin_{entityName}_edit` → Éditer
- `admin_{entityName}_delete` → Supprimer
- `admin_{entityName}_detail` → Voir détails

**Action** : Remplacer tous les `linkToCrud()` par `linkToRoute()` avec les noms corrects.

---

#### 3. Pagination - `setPaginationPageSize()` Inexistante

**Problème** : `Call to undefined method setPaginationPageSize()`

**Cause** : Mauvais nom de méthode en v5

**Solution** :
```php
// ❌ MAUVAIS
$crud->setPaginationPageSize(25);

// ✅ BON
$crud->setPaginatorPageSize(25);
```

**Action** : Dans tous les CRUD controllers, remplacer `setPaginationPageSize()` par `setPaginatorPageSize()`.

---

#### 4. Panneaux de Formulaire - `FormField::addPanel()` Inexistante

**Problème** : `Call to undefined method addPanel()`

**Cause** : EasyAdmin v5 n'utilise pas `FormField::addPanel()`

**Solution** :
```php
// ❌ MAUVAIS
FormField::addPanel('Informations Civiles'),
IdField::new('id')->hideOnForm(),
TextField::new('nom'),

// ✅ BON - Les champs s'affichent directement, sans panneaux
IdField::new('id')->hideOnForm(),
TextField::new('nom'),
TextField::new('prenom'),
// Les panneaux visuels se font via CSS/Bootstrap si nécessaire
```

**Action** : Supprimer tous les appels `FormField::addPanel()` du code.

---

#### 5. CollectionField - `useEntriesForm()` N'existe Pas

**Problème** : `Call to undefined method useEntriesForm()`

**Cause** : Mauvaise API pour EasyAdmin v5

**Solution** :
```php
// ❌ MAUVAIS
CollectionField::new('profils')
    ->useEntriesForm()
    ->setEntryIsComplex(true),

// ✅ BON - Simpler et fonctionne
CollectionField::new('profils')
    ->setHelp('Les rôles associés à cet utilisateur'),
```

**Action** : Simplifier les configurations CollectionField.

---

#### 6. Hachage des Mots de Passe - Stocker en Clair

**Problème** : Les mots de passe sont stockés en clair dans la base de données

**Cause** : Pas d'appel au PasswordHasher lors de la création/modification

**Solution Correcte** :
```php
class LoginCrudController extends AbstractCrudController
{
    public function __construct(private PasswordHasherFactoryInterface $passwordHasherFactory) {}

    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if ($entityInstance instanceof Login) {
            $hasher = $this->passwordHasherFactory->getPasswordHasher(Login::class);
            $hashedPassword = $hasher->hash($entityInstance->getPassword());
            $entityInstance->setPassword($hashedPassword);
        }
        parent::persistEntity($em, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if ($entityInstance instanceof Login) {
            // Vérifier que c'est un nouveau password (pas déjà hashé)
            if ($entityInstance->getPassword() && !str_starts_with($entityInstance->getPassword(), '$2')) {
                $hasher = $this->passwordHasherFactory->getPasswordHasher(Login::class);
                $hashedPassword = $hasher->hash($entityInstance->getPassword());
                $entityInstance->setPassword($hashedPassword);
            }
        }
        parent::updateEntity($em, $entityInstance);
    }
}
```

**Action** : Implémenter le hachage dans `persistEntity()` et `updateEntity()` pour tous les contrôleurs avec passwords.

---

#### 7. Dashboard Héritant du Route au Lieu du Template

**Problème** : Dashboard affiche la page par défaut "Welcome to EasyAdmin"

**Cause** : Utiliser `parent::index()` affiche le template par défaut

**Solution** :
```php
// ❌ MAUVAIS
public function index(): Response
{
    return parent::index();  // Affiche Welcome to EasyAdmin
}

// ✅ BON - Rendre un template custom qui hérite @EasyAdmin/layout.html.twig
public function index(): Response
{
    return $this->render('admin/dashboard.html.twig');
}

// Dans templates/admin/dashboard.html.twig
{% extends '@EasyAdmin/layout.html.twig' %}
{% block main %}
    <!-- Votre contenu personnalisé -->
{% endblock %}
```

**Action** : Créer un template personnalisé qui hérite de `@EasyAdmin/layout.html.twig` pour conserver le menu/layout EasyAdmin.

---

#### 8. Validations Manquantes sur les Entités

**Problème** : La base de données accepte des données invalides (null, vides, etc.)

**Cause** : Pas de constraints de validation sur les entités

**Solution** :
```php
use Symfony\Component\Validator\Constraints as Assert;

class Login
{
    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'L\'email ne peut pas être vide')]
    #[Assert\Email(message: 'L\'email doit être valide')]
    private ?string $Mail = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le mot de passe ne peut pas être vide')]
    #[Assert\Length(min: 6, minMessage: 'Le mot de passe doit faire au moins 6 caractères')]
    private ?string $Password = null;
}

class Utilisateur
{
    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le nom ne peut pas être vide')]
    private ?string $Nom = null;

    #[ORM\ManyToOne(targetEntity: Login::class)]
    #[Assert\NotNull(message: 'Un utilisateur doit avoir un login')]
    private ?Login $login = null;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Profil::class)]
    #[Assert\Count(min: 1, minMessage: 'Un utilisateur doit avoir au moins un profil')]
    private Collection $profils;
}
```

**Action** : Ajouter les contraintes `Assert\*` sur toutes les propriétés critiques.

---

#### 9. Interface DashboardController Incomplète

**Problème** : Dashboard ne charge pas le menu ou affiche des erreurs

**Cause** : Configuration incomplète du DashboardController

**Solution Complète** :
```php
<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Institut de Greffe - Admin')
            ->setFaviconPath('images/favicon.svg');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::linkToRoute('Logins', 'fas fa-key', 'admin_login_index');
        yield MenuItem::linkToRoute('Utilisateurs', 'fas fa-users', 'admin_utilisateur_index');
        yield MenuItem::linkToRoute('Profils', 'fas fa-user-tag', 'admin_profil_index');
    }
}
```

**Points critiques** :
- `#[AdminDashboard(routePath: '/admin', routeName: 'admin')]` - OBLIGATOIRE
- `public function index(): Response` - OBLIGATOIRE
- `public function configureDashboard(): Dashboard` - OBLIGATOIRE
- `public function configureMenuItems(): iterable` - OBLIGATOIRE
- Utiliser `MenuItem::linkToRoute()` avec les noms de routes EasyAdmin

---

### ✅ CHECKLIST DE CONFIGURATION CORRECTE

Avant de mettre en production, vérifier :

- [ ] **DashboardController** possède l'attribut `#[AdminDashboard(routePath: '/admin', routeName: 'admin')]`
- [ ] **DashboardController** a une méthode `index()` qui rend un template
- [ ] **Tous les CRUD controllers** héritent de `AbstractCrudController`
- [ ] **Tous les CRUD controllers** implémentent `getEntityFqcn()` et `configureFields()`
- [ ] **Menu items** utilisent `linkToRoute()` avec les bons noms (admin_[entity]_index)
- [ ] **Pagination** utilise `setPaginatorPageSize()` et non `setPaginationPageSize()`
- [ ] **Mots de passe** sont hashés via `persistEntity()` et `updateEntity()`
- [ ] **Validations** sont présentes sur les entités avec `Assert\*` constraints
- [ ] **Routes EasyAdmin** sont configurées dans `config/routes/easyadmin.yaml` :
  ```yaml
  easyadmin:
      resource: .
      type: easyadmin.routes
  ```
- [ ] **Routes.yaml** inclut la route EasyAdmin :
  ```yaml
  easyadmin:
      resource: routes/easyadmin.yaml
  ```
- [ ] **Cache vidé** après tous les changements : `php bin/console cache:clear`

---

### 📚 FICHIERS CLÉS À VÉRIFIER

1. **src/Controller/Admin/DashboardController.php** - Configuration du dashboard
2. **src/Controller/Admin/*CrudController.php** - Tous les CRUD
3. **config/routes.yaml** - Inclusion des routes EasyAdmin
4. **config/routes/easyadmin.yaml** - Déclaration de type easyadmin.routes
5. **config/packages/easy_admin.yaml** - Configuration des entités
6. **src/Entity/*.php** - Validations et decorateurs

---

### 🔗 ROUTES EASYADMIN CORRECTES

```bash
# Vérifier les routes disponibles
docker compose exec php php bin/console debug:router | grep admin

# Devrait afficher (exemple) :
# admin                            GET|POST  /admin
# admin_login_index               GET       /admin/login
# admin_login_new                 GET|POST  /admin/login/new
# admin_login_edit                GET|POST  /admin/login/{entityId}/edit
# admin_utilisateur_index         GET       /admin/utilisateur
# admin_utilisateur_new           GET|POST  /admin/utilisateur/new
# admin_utilisateur_edit          GET|POST  /admin/utilisateur/{entityId}/edit
```

---

## Ressources Utiles


- 📚 [Documentation Officielle EasyAdmin](https://symfony.com/bundles/EasyAdminBundle/current/index.html)
- 🎥 [Screencast SymfonyCasts](https://symfonycasts.com/screencast/easyadminbundle)
- 🔧 [API Reference CRUD](https://symfony.com/bundles/EasyAdminBundle/current/crud.html)
- 🎨 [Design & Customization](https://symfony.com/bundles/EasyAdminBundle/current/design.html)
- 🔐 [Security & Permissions](https://symfony.com/bundles/EasyAdminBundle/current/security.html)
- 📋 [Fields Reference](https://symfony.com/bundles/EasyAdminBundle/current/fields.html)

---

**Dernière mise à jour** : 13 mars 2026
**Auteur** : Institut de Greffe de Foie Limoges
**Version EasyAdmin** : 5.x
