# CRUD Médecin - Tutoriel Complet de A à Z

## 📋 Table des matières
1. [Prérequis](#prérequis)
2. [Étape 1 : Créer le contrôleur](#étape-1--créer-le-contrôleur)
3. [Étape 2 : Créer le layout](#étape-2--créer-le-layout)
4. [Étape 3 : Créer les templates](#étape-3--créer-les-templates)
5. [Étape 4 : Configurer Twig](#étape-4--configurer-twig)
6. [Étape 5 : Ajouter le lien vers Patients](#étape-5--ajouter-le-lien-vers-les-patients)
7. [Étape 6 : Tester](#étape-6--tester)

---

## Prérequis

Vous devez avoir :
- ✅ Symfony 8.0+ installé
- ✅ Les entités `Utilisateur`, `Login`, `Profil` créées
- ✅ Un médecin connecté avec le rôle `ROLE_MEDECIN`

---

## Étape 1 : Créer le contrôleur

### 1.1 Créer le fichier

**Chemin** : `src/Controller/Medecin/PatientsFormController.php`

```php
<?php

namespace App\Controller\Medecin;

use App\Entity\Login;
use App\Entity\Profil;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_MEDECIN')]
#[Route('/medecin/patients')]
class PatientsFormController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PasswordHasherFactoryInterface $hasherFactory
    ) {}

    /**
     * Affiche la liste de tous les patients (users avec ROLE_PATIENT)
     */
    #[Route('/liste', name: 'medecin_patients_liste', methods: ['GET'])]
    public function liste(): Response
    {
        $patients = $this->entityManager->getRepository(Utilisateur::class)
            ->createQueryBuilder('u')
            ->leftJoin('u.profils', 'p')
            ->where('p.Role = :role')
            ->setParameter('role', 'ROLE_PATIENT')
            ->orderBy('u.Nom', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('medecin/patients_list.html.twig', [
            'patients' => $patients
        ]);
    }

    /**
     * Affiche les détails d'un patient
     */
    #[Route('/{id}/voir', name: 'medecin_patients_voir', methods: ['GET'])]
    public function voir($id): Response
    {
        $patient = $this->entityManager->getRepository(Utilisateur::class)->find($id);

        if (!$patient) {
            throw $this->createNotFoundException('Patient introuvable');
        }

        // Vérifier que c'est bien un patient (ROLE_PATIENT)
        $isProfil = $patient->getProfils()->exists(function($key, $element) {
            return $element->getRole() === 'ROLE_PATIENT';
        });

        if (!$isProfil) {
            throw $this->createAccessDeniedException('Accès refusé');
        }

        return $this->render('medecin/patients_voir.html.twig', [
            'patient' => $patient
        ]);
    }

    /**
     * Crée un nouveau patient
     */
    #[Route('/creer', name: 'medecin_patients_creer', methods: ['GET', 'POST'])]
    public function creer(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            // 1. Récupérer les données du formulaire
            $nom = $request->request->get('nom');
            $prenom = $request->request->get('prenom');
            $ville_res = $request->request->get('ville_res');
            $cp = $request->request->get('cp');
            $mail = $request->request->get('mail');
            $password = $request->request->get('password');

            // 2. Créer et sauvegarder le Login
            $login = new Login();
            $login->setMail($mail);

            // Hasher le mot de passe
            $hashedPassword = $this->hasherFactory
                ->getPasswordHasher(Login::class)
                ->hash($password);
            $login->setPassword($hashedPassword);

            $this->entityManager->persist($login);
            $this->entityManager->flush();

            // 3. Créer et sauvegarder l'Utilisateur
            $utilisateur = new Utilisateur();
            $utilisateur->setNom($nom);
            $utilisateur->setPrenom($prenom);
            $utilisateur->setVilleRes($ville_res);
            $utilisateur->setCP($cp);
            $utilisateur->setLogin($login);

            $this->entityManager->persist($utilisateur);
            $this->entityManager->flush();

            // 4. Créer et sauvegarder le Profil (ROLE_PATIENT obligatoire)
            $profil = new Profil();
            $profil->setRole('ROLE_PATIENT');
            $profil->setUtilisateur($utilisateur);

            $this->entityManager->persist($profil);
            $this->entityManager->flush();

            return $this->redirectToRoute('medecin_patients_liste');
        }

        return $this->render('medecin/patients_form.html.twig', [
            'title' => 'Ajouter un patient'
        ]);
    }

    /**
     * Modifie un patient existant
     */
    #[Route('/{id}/modifier', name: 'medecin_patients_modifier', methods: ['GET', 'POST'])]
    public function modifier($id, Request $request): Response
    {
        $patient = $this->entityManager->getRepository(Utilisateur::class)->find($id);

        if (!$patient) {
            throw $this->createNotFoundException('Patient introuvable');
        }

        // Vérifier que c'est bien un patient (ROLE_PATIENT)
        $isProfil = $patient->getProfils()->exists(function($key, $element) {
            return $element->getRole() === 'ROLE_PATIENT';
        });

        if (!$isProfil) {
            throw $this->createAccessDeniedException('Accès refusé');
        }

        if ($request->isMethod('POST')) {
            // 1. Mettre à jour les infos civiles
            $patient->setNom($request->request->get('nom'));
            $patient->setPrenom($request->request->get('prenom'));
            $patient->setVilleRes($request->request->get('ville_res'));
            $patient->setCP($request->request->get('cp'));

            // 2. Mettre à jour le Login
            $login = $patient->getLogin();
            if ($login) {
                $login->setMail($request->request->get('mail'));

                // Mettre à jour le mot de passe SI fourni
                $password = $request->request->get('password');
                if ($password) {
                    // Vérifier que ce n'est pas déjà un hash (bcrypt commence par $2)
                    if (!str_starts_with($password, '$2')) {
                        $hashedPassword = $this->hasherFactory
                            ->getPasswordHasher(Login::class)
                            ->hash($password);
                        $login->setPassword($hashedPassword);
                    }
                }
            }

            $this->entityManager->flush();

            return $this->redirectToRoute('medecin_patients_liste');
        }

        return $this->render('medecin/patients_modifier.html.twig', [
            'patient' => $patient
        ]);
    }

    /**
     * Supprime un patient
     */
    #[Route('/{id}/supprimer', name: 'medecin_patients_supprimer', methods: ['POST'])]
    public function supprimer($id, Request $request): Response
    {
        $patient = $this->entityManager->getRepository(Utilisateur::class)->find($id);

        if (!$patient) {
            throw $this->createNotFoundException('Patient introuvable');
        }

        // Vérifier que c'est bien un patient (ROLE_PATIENT)
        $isProfil = $patient->getProfils()->exists(function($key, $element) {
            return $element->getRole() === 'ROLE_PATIENT';
        });

        if (!$isProfil) {
            throw $this->createAccessDeniedException('Accès refusé');
        }

        // Supprimer le Login associé
        $login = $patient->getLogin();
        if ($login) {
            $this->entityManager->remove($login);
        }

        // Supprimer le Utilisateur (les Profils se suppriment en cascade)
        $this->entityManager->remove($patient);
        $this->entityManager->flush();

        return $this->redirectToRoute('medecin_patients_liste');
    }
}
```

### 1.2 Vérifier que le fichier est bien créé

```bash
ls src/Controller/Medecin/PatientsFormController.php
```

---

## Étape 2 : Créer le layout

### 2.1 Créer le fichier `layout.html.twig`

**Chemin** : `templates/medecin/layout.html.twig`

```twig
{% extends 'base.html.twig' %}

{% block title %}Médecin - Patients{% endblock %}

{% block stylesheets %}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .sidebar {
            background-color: #fff;
            border-right: 1px solid #e9ecef;
            min-height: calc(100vh - 56px);
            padding: 20px 0;
        }
        
        .sidebar .nav-link {
            color: #495057;
            border-left: 3px solid transparent;
            padding: 12px 20px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            background-color: #f8f9fa;
            color: #667eea;
            border-left-color: #667eea;
        }
        
        .sidebar .nav-link.active {
            background-color: #f0f1ff;
            color: #667eea;
            border-left-color: #667eea;
            font-weight: 500;
        }
        
        .main-content {
            padding: 30px;
            background-color: #f5f7fa;
        }
        
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 8px;
        }
        
        .card-header {
            border-radius: 8px 8px 0 0 !important;
        }
        
        h2 {
            color: #333;
            font-weight: 600;
            margin-bottom: 25px;
        }
        
        h5 {
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 0.5rem;
            margin-top: 25px;
            margin-bottom: 15px;
        }
        
        .btn-primary {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .btn-primary:hover {
            background-color: #5568d3;
            border-color: #5568d3;
        }
        
        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #f8f9ff;
        }
        
        .table-hover tbody tr:hover {
            background-color: #f0f1ff;
        }
        
        .badge {
            padding: 6px 12px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .text-danger {
            color: #dc3545;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-group-sm .btn {
            padding: 4px 8px;
            font-size: 0.75rem;
        }
    </style>
{% endblock %}

{% block javascripts %}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {{ parent() }}
{% endblock %}

{% block body %}
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ path('app_medecin_accueil') }}">
            <i class="fas fa-hospital"></i> Médecin
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ path('app_logout') }}">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 sidebar">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ path('app_medecin_accueil') }}">
                        <i class="fas fa-home"></i> Tableau de bord
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ path('medecin_patients_liste') }}">
                        <i class="fas fa-users"></i> Voir Patients
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ path('medecin_patients_creer') }}">
                        <i class="fas fa-user-plus"></i> Ajouter Patient
                    </a>
                </li>
            </ul>
        </nav>

        <main class="col-md-10 main-content">
            {% block main %}{% endblock %}
        </main>
    </div>
</div>
{% endblock %}
```

### 2.2 Explication du layout

```
{% extends 'base.html.twig' %}
```
↑ Hérite de base.html.twig (doctype, head, etc)

```
{% block stylesheets %} ... {% endblock %}
```
↑ Ajoute Bootstrap et Font Awesome + styles personnalisés

```
<nav class="navbar"> ... </nav>
```
↑ Barre de navigation avec logo et bouton déconnexion

```
<nav class="col-md-2 sidebar"> ... </nav>
```
↑ Menu latéral avec 3 liens (Tableau de bord, Voir Patients, Ajouter Patient)

```
<main class="col-md-10 main-content">
    {% block main %}{% endblock %}
</main>
```
↑ Zone principale où vont s'afficher les templates

---

## Étape 3 : Créer les templates

### 3.1 Template LISTE : `patients_list.html.twig`

**Chemin** : `templates/medecin/patients_list.html.twig`

```twig
{% extends '@medecin/layout.html.twig' %}

{% block main %}
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Liste de vos Patients</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ path('medecin_patients_creer') }}" class="btn btn-primary">+ Ajouter Patient</a>
        </div>
    </div>

    {% if patients|length == 0 %}
        <div class="alert alert-info">
            Aucun patient créé. <a href="{{ path('medecin_patients_creer') }}">Créer le premier patient</a>
        </div>
    {% else %}
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Ville</th>
                        <th>Code Postal</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {% for patient in patients %}
                    <tr>
                        <td>{{ patient.nom }}</td>
                        <td>{{ patient.prenom }}</td>
                        <td>{{ patient.villeRes }}</td>
                        <td>{{ patient.CP }}</td>
                        <td>{{ patient.login.mail }}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ path('medecin_patients_voir', {id: patient.idUtilisateur}) }}" class="btn btn-info" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ path('medecin_patients_modifier', {id: patient.idUtilisateur}) }}" class="btn btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ path('medecin_patients_supprimer', {id: patient.idUtilisateur}) }}" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce patient ?');">
                                    <button type="submit" class="btn btn-danger btn-sm" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    {% endif %}
</div>
{% endblock %}
```

**Explication** :
- `{% extends '@medecin/layout.html.twig' %}` = utilise le layout
- `{% if patients|length == 0 %}` = si pas de patients
- `{% for patient in patients %}` = boucle sur les patients
- `{{ path(...) }}` = génère les URLs avec les noms de routes

### 3.2 Template VOIR : `patients_voir.html.twig`

**Chemin** : `templates/medecin/patients_voir.html.twig`

```twig
{% extends '@medecin/layout.html.twig' %}

{% block main %}
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Détails du Patient</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ path('medecin_patients_modifier', {id: patient.idUtilisateur}) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ path('medecin_patients_liste') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informations Civiles</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Nom:</strong> {{ patient.nom }}
                        </div>
                        <div class="col-md-6">
                            <strong>Prénom:</strong> {{ patient.prenom }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Ville:</strong> {{ patient.villeRes }}
                        </div>
                        <div class="col-md-6">
                            <strong>Code Postal:</strong> {{ patient.CP }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Authentification</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <strong>Email:</strong> {{ patient.login.mail }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Rôle</h5>
                </div>
                <div class="card-body">
                    {% if patient.profils|length > 0 %}
                        <span class="badge bg-info">{{ patient.profils.first.role }}</span>
                    {% endif %}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border-radius: 8px;
    }
    
    .card-header {
        border-radius: 8px 8px 0 0 !important;
        font-weight: 600;
    }
    
    .card-header h5 {
        color: white;
        border: none;
        margin: 0;
        padding: 0;
    }
    
    h2 {
        color: #333;
        font-weight: 600;
    }
    
    strong {
        color: #333;
    }
</style>
{% endblock %}
```

### 3.3 Template CRÉER : `patients_form.html.twig`

**Chemin** : `templates/medecin/patients_form.html.twig`

```twig
{% extends '@medecin/layout.html.twig' %}

{% block main %}
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <h2>Ajouter un nouveau patient</h2>
            
            <form method="POST" class="mt-4">
                <!-- Données Civiles -->
                <h5 class="mt-4 mb-3">Informations Civiles</h5>
                
                <div class="form-group mb-3">
                    <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                    <input type="text" id="nom" name="nom" class="form-control" required>
                </div>

                <div class="form-group mb-3">
                    <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                    <input type="text" id="prenom" name="prenom" class="form-control" required>
                </div>

                <div class="form-group mb-3">
                    <label for="ville_res" class="form-label">Ville <span class="text-danger">*</span></label>
                    <input type="text" id="ville_res" name="ville_res" class="form-control" required>
                </div>

                <div class="form-group mb-3">
                    <label for="cp" class="form-label">Code Postal <span class="text-danger">*</span></label>
                    <input type="text" id="cp" name="cp" class="form-control" required>
                </div>

                <!-- Authentification -->
                <h5 class="mt-4 mb-3">Authentification</h5>
                
                <div class="form-group mb-3">
                    <label for="mail" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" id="mail" name="mail" class="form-control" required>
                </div>

                <div class="form-group mb-3">
                    <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                    <input type="password" id="password" name="password" class="form-control" required minlength="6">
                    <small class="form-text text-muted">Minimum 6 caractères</small>
                </div>

                <!-- Boutons -->
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Créer le patient</button>
                    <a href="{{ path('medecin_patients_liste') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .text-danger {
        color: #dc3545;
    }
    
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    h5 {
        color: #333;
        border-bottom: 2px solid #667eea;
        padding-bottom: 0.5rem;
    }
</style>
{% endblock %}
```

### 3.4 Template MODIFIER : `patients_modifier.html.twig`

**Chemin** : `templates/medecin/patients_modifier.html.twig`

```twig
{% extends '@medecin/layout.html.twig' %}

{% block main %}
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <h2>Modifier {{ patient.prenom }} {{ patient.nom }}</h2>
            
            <form method="POST" class="mt-4">
                <!-- Données Civiles -->
                <h5 class="mt-4 mb-3">Informations Civiles</h5>
                
                <div class="form-group mb-3">
                    <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                    <input type="text" id="nom" name="nom" class="form-control" value="{{ patient.nom }}" required>
                </div>

                <div class="form-group mb-3">
                    <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                    <input type="text" id="prenom" name="prenom" class="form-control" value="{{ patient.prenom }}" required>
                </div>

                <div class="form-group mb-3">
                    <label for="ville_res" class="form-label">Ville <span class="text-danger">*</span></label>
                    <input type="text" id="ville_res" name="ville_res" class="form-control" value="{{ patient.villeRes }}" required>
                </div>

                <div class="form-group mb-3">
                    <label for="cp" class="form-label">Code Postal <span class="text-danger">*</span></label>
                    <input type="text" id="cp" name="cp" class="form-control" value="{{ patient.CP }}" required>
                </div>

                <!-- Authentification -->
                <h5 class="mt-4 mb-3">Authentification</h5>
                
                <div class="form-group mb-3">
                    <label for="mail" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" id="mail" name="mail" class="form-control" value="{{ patient.login.mail }}" required>
                </div>

                <div class="form-group mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control" minlength="6">
                    <small class="form-text text-muted">Laissez vide pour conserver le mot de passe actuel. Sinon, entrez un nouveau (minimum 6 caractères)</small>
                </div>

                <!-- Boutons -->
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                    <a href="{{ path('medecin_patients_liste') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .text-danger {
        color: #dc3545;
    }
    
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    h5 {
        color: #333;
        border-bottom: 2px solid #667eea;
        padding-bottom: 0.5rem;
    }
</style>
{% endblock %}
```

---

## Étape 4 : Configurer Twig

### 4.1 Ajouter le namespace `@medecin`

**Fichier** : `config/packages/twig.yaml`

**Avant** :
```yaml
twig:
    file_name_pattern: '*.twig'
    paths:
        '%kernel.project_dir%/templates/admin': admin

when@test:
    twig:
        strict_variables: true
```

**Après** :
```yaml
twig:
    file_name_pattern: '*.twig'
    paths:
        '%kernel.project_dir%/templates/admin': admin
        '%kernel.project_dir%/templates/medecin': medecin

when@test:
    twig:
        strict_variables: true
```

**Explication** :
```yaml
'%kernel.project_dir%/templates/medecin': medecin
```
↑ Dit à Twig que `@medecin/` = `templates/medecin/`

---

## Étape 5 : Ajouter le lien vers les Patients

### 5.1 Mettre à jour le tableau de bord médecin

**Fichier** : `templates/home/medecin/accueil.html.twig`

Trouver cette ligne :
```html
<a href="#" class="medecin-card-btn">Accéder</a>
```

Et la remplacer par :
```html
<a href="{{ path('medecin_patients_liste') }}" class="medecin-card-btn">Accéder</a>
```

**Avant de remplacer, votre code ressemble à ça** :
```html
<!-- Mes Patients -->
<div class="medecin-card">
    <div class="medecin-card-icon">👥</div>
    <h3>Patients</h3>
    <p>Consultez la liste de vos patients</p>
    <a href="#" class="medecin-card-btn">Accéder</a>
</div>
```

**Après le remplacement** :
```html
<!-- Mes Patients -->
<div class="medecin-card">
    <div class="medecin-card-icon">👥</div>
    <h3>Patients</h3>
    <p>Consultez la liste de vos patients</p>
    <a href="{{ path('medecin_patients_liste') }}" class="medecin-card-btn">Accéder</a>
</div>
```

---

## Étape 6 : Tester

### 6.1 Vider le cache

```bash
docker compose exec php php bin/console cache:clear
```

**Résultat attendu** :
```
[OK] Cache for the "dev" environment (debug=true) was successfully cleared.
```

### 6.2 Tester les routes

Connectez-vous en tant que médecin et allez à :

#### 1️⃣ Liste des patients
```
http://localhost/medecin/patients/liste
```

**Attendu** : 
- Voir un tableau vide (ou avec les patients créés)
- Bouton "+ Ajouter Patient"

#### 2️⃣ Créer un patient
```
http://localhost/medecin/patients/creer
```

**Attendu** :
- Formulaire avec champs : Nom, Prénom, Ville, CP, Email, Mot de passe
- 2 boutons : Créer le patient, Annuler

**À faire** :
1. Remplir les champs
2. Cliquer "Créer le patient"
3. Être redirigé vers la liste
4. Voir le patient nouvellement créé

#### 3️⃣ Voir un patient
```
http://localhost/medecin/patients/1/voir
```
(remplacer 1 par l'ID d'un vrai patient)

**Attendu** :
- Cartes avec les infos du patient
- Boutons "Modifier" et "Retour"

#### 4️⃣ Modifier un patient
```
http://localhost/medecin/patients/1/modifier
```

**Attendu** :
- Formulaire pré-rempli avec les valeurs actuelles
- Champ "Mot de passe" optionnel

**À faire** :
1. Changer un champ
2. Cliquer "Enregistrer les modifications"
3. Être redirigé vers la liste
4. Voir les changements

#### 5️⃣ Supprimer un patient
Depuis la liste, cliquer l'icône 🗑️

**Attendu** :
- Confirmation : "Êtes-vous sûr ?"
- Si oui, la ligne disparaît

### 6.3 Erreurs courantes et solutions

#### ❌ Erreur : "Route 'medecin_patients_liste' not found"

**Cause** : Cache pas vidé

**Solution** :
```bash
docker compose exec php php bin/console cache:clear
```

#### ❌ Erreur : "Class has no field named 'Role'"

**Cause** : Vous avez écrit `p.role` au lieu de `p.Role` dans la requête DQL

**Solution** : Vérifier la casse dans `PatientsFormController.php` ligne 39

#### ❌ Erreur : "Unable to find template '@medecin/layout.html.twig'"

**Cause** : Configuration Twig pas créée

**Solution** : Vérifier que vous avez bien ajouté les lignes dans `config/packages/twig.yaml`

#### ❌ Les patients ne s'affichent pas

**Cause** : Pas de patients avec ROLE_PATIENT en base de données

**Solution** : Créer un patient via le formulaire de création

#### ❌ Accès refusé (403)

**Cause** : L'utilisateur n'a pas ROLE_MEDECIN

**Solution** : Vérifier que l'utilisateur a un Profil avec le rôle `ROLE_MEDECIN`

---

## Résumé des fichiers créés

| Fichier | Description |
|---------|------------|
| `src/Controller/Medecin/PatientsFormController.php` | Contrôleur avec 5 méthodes (liste, voir, creer, modifier, supprimer) |
| `templates/medecin/layout.html.twig` | Layout général (navbar, sidebar, styles) |
| `templates/medecin/patients_list.html.twig` | Affiche le tableau des patients |
| `templates/medecin/patients_voir.html.twig` | Affiche les détails d'un patient |
| `templates/medecin/patients_form.html.twig` | Formulaire de création |
| `templates/medecin/patients_modifier.html.twig` | Formulaire de modification |
| `config/packages/twig.yaml` | Ajout du namespace `@medecin` |
| `templates/home/medecin/accueil.html.twig` | Lien vers la liste des patients |

---

## Checklist finale

- ✅ Fichier contrôleur créé : `src/Controller/Medecin/PatientsFormController.php`
- ✅ Layout créé : `templates/medecin/layout.html.twig`
- ✅ 4 templates créés :
  - `patients_list.html.twig`
  - `patients_voir.html.twig`
  - `patients_form.html.twig`
  - `patients_modifier.html.twig`
- ✅ Twig.yaml modifié (namespace `@medecin`)
- ✅ Lien ajouté au tableau de bord médecin
- ✅ Cache vidé
- ✅ Toutes les routes testées

**Vous pouvez maintenant gérer les patients comme un médecin !** 🎉

---

**Dernière mise à jour** : 13 mars 2026
