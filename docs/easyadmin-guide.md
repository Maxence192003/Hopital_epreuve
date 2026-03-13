# Guide EasyAdmin - Institut de Greffe de Foie Limoges

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

Ajoutez la route EasyAdmin dans `config/routes.yaml` :

```yaml
admin:
    resource: '@EasyAdminBundle/config/routes.yaml'
    prefix: /admin
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

---

## Création des CRUD Controllers

Un **CRUD Controller** gère les opérations Create, Read, Update, Delete pour une entité.

### Générer un CRUD avec la commande make

```bash
php bin/console make:easy-admin:crud
```

Cette commande vous demande quelle entité gérer et crée le controller automatiquement.

### Exemple : CRUD des Utilisateurs

**Fichier** : `src/Controller/Admin/UtilisateurCrudController.php`

```php
<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class UtilisateurCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Utilisateur::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('login'),
            EmailField::new('email'),
            TextField::new('nom'),
            TextField::new('prenom'),
            ChoiceField::new('role')
                ->setChoices([
                    'Administrateur' => 'ROLE_ADMIN',
                    'Médecin' => 'ROLE_MEDECIN',
                    'Infirmier' => 'ROLE_INFIRMIER',
                ]),
            DateTimeField::new('createdAt')->hideOnForm(),
        ];
    }
}
```

### Expliquer la structure

- `getEntityFqcn()` : Retourne le nom complet de la classe de l'entité gérée
- `configureFields()` : Définit les champs à afficher dans les formulaires et listes
- `IdField::hideOnForm()` : Cache l'ID du formulaire (généré auto)
- `ChoiceField` : Liste déroulante avec valeurs prédéfinies

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
