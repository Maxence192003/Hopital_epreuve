# 🏥 Guide : Implémenter une page dédiée aux Médecins

## 📋 Vue d'ensemble

Ce guide vous permettra de :
1. ✅ Ajouter un rôle **MEDECIN** au système
2. ✅ Configurer les utilisateurs en tant que médecins
3. ✅ Créer un contrôleur pour le tableau de bord médecin
4. ✅ Créer une page accueil dédiée aux médecins
5. ✅ Rediriger automatiquement les médecins vers leur page

---

## 🔧 Étape 1 : Vérifier/Modifier l'entité `Login`

L'entité `Login` doit retourner les rôles associés aux profils de l'utilisateur.

**Fichier :** `src/Entity/Login.php`

Vérifiez que la méthode `getRoles()` existe. Si elle n'existe pas, ajoutez-la :

```php
public function getRoles(): array
{
    $roles = ['ROLE_USER']; // Rôle par défaut
    
    $utilisateurs = $this->getUtilisateurs();
    foreach ($utilisateurs as $utilisateur) {
        foreach ($utilisateur->getProfils() as $profil) {
            $role = 'ROLE_' . strtoupper($profil->getRole());
            if (!in_array($role, $roles)) {
                $roles[] = $role;
            }
        }
    }
    
    return $roles;
}

public function eraseCredentials(): void
{
    // Si vous stockez une donnée sensible temporaire, la supprimer ici
}
```

**Explication :** 
- Cette méthode récupère tous les rôles des profils associés à cet utilisateur
- Elle ajoute le préfixe `ROLE_` requis par Symfony
- Un utilisateur peut avoir plusieurs rôles (ex: ROLE_ADMIN, ROLE_MEDECIN)

---

## 🎭 Étape 2 : Créer le Contrôleur Médecin

Créez un nouveau fichier : `src/Controller/MedecinController.php`

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/medecin')]
#[IsGranted('ROLE_MEDECIN')]
class MedecinController extends AbstractController
{
    #[Route('', name: 'app_medecin_accueil')]
    public function accueil(): Response
    {
        // Récupère l'utilisateur connecté
        $user = $this->getUser();
        
        // Récupère les informations du médecin via la relation Login -> Utilisateur
        $utilisateurs = $user->getUtilisateurs();
        $medecin = $utilisateurs->first() ?? null;
        
        return $this->render('medecin/accueil.html.twig', [
            'medecin' => $medecin,
            'user' => $user,
        ]);
    }

    #[Route('/mes-patients', name: 'app_medecin_patients')]
    public function mesPatientsAction(): Response
    {
        // À implémenter : afficher la liste des patients du médecin
        return $this->render('medecin/patients.html.twig', [
            // Vos données
        ]);
    }

    #[Route('/dossiers', name: 'app_medecin_dossiers')]
    public function dossiersPatientsAction(): Response
    {
        // À implémenter : gérer les dossiers patients
        return $this->render('medecin/dossiers.html.twig', [
            // Vos données
        ]);
    }
}
```

**Explication :**
- `#[Route('/medecin')]` : toutes les routes commencent par `/medecin`
- `#[IsGranted('ROLE_MEDECIN')]` : seuls les utilisateurs avec ce rôle peuvent accéder
- `accueil()` : la page d'accueil du médecin

---

## 🎨 Étape 3 : Créer le Template Médecin

Créez: `templates/medecin/accueil.html.twig`

```twig
{% extends "base.html.twig" %}

{% block title %}Accueil Médecin - Hôpital{% endblock %}

{% block body %}
    <div class="container-fluid py-5">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">
                            👨‍⚕️ Bienvenue Dr. {{ medecin.prenom }} {{ medecin.nom|upper }}
                        </h2>
                    </div>
                    <div class="card-body p-5">
                        <div class="row mt-4">
                            <!-- Carte 1 : Mes Patients -->
                            <div class="col-md-4 mb-4">
                                <div class="card border-0 shadow-sm text-center">
                                    <div class="card-body">
                                        <h3 class="card-title">
                                            <i class="fas fa-users text-info"></i> Mes Patients
                                        </h3>
                                        <a href="{{ path('app_medecin_patients') }}" class="btn btn-info mt-3">
                                            Voir la liste
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Carte 2 : Dossiers Patients -->
                            <div class="col-md-4 mb-4">
                                <div class="card border-0 shadow-sm text-center">
                                    <div class="card-body">
                                        <h3 class="card-title">
                                            <i class="fas fa-folder-medical text-warning"></i> Dossiers
                                        </h3>
                                        <a href="{{ path('app_medecin_dossiers') }}" class="btn btn-warning mt-3">
                                            Consulter
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Carte 3 : Notes Médicales -->
                            <div class="col-md-4 mb-4">
                                <div class="card border-0 shadow-sm text-center">
                                    <div class="card-body">
                                        <h3 class="card-title">
                                            <i class="fas fa-clipboard text-success"></i> Notes
                                        </h3>
                                        <a href="#" class="btn btn-success mt-3">
                                            En attente
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-5">

                        <!-- Section Informations -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h4>📍 Informations Personnelles</h4>
                                <p><strong>Nom :</strong> {{ medecin.nom }}</p>
                                <p><strong>Prénom :</strong> {{ medecin.prenom }}</p>
                                <p><strong>Email :</strong> {{ user.mail }}</p>
                                <p><strong>Ville :</strong> {{ medecin.villeRes }}</p>
                                <p><strong>Code Postal :</strong> {{ medecin.cp }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4>🔐 Statut d'Accès</h4>
                                <p><strong>Rôle :</strong> <span class="badge bg-primary">MÉDECIN</span></p>
                                <p><strong>Statut :</strong> <span class="badge bg-success">Actif</span></p>
                                <p><strong>Connexion sécurisée :</strong> ✅ Oui</p>
                                <a href="{{ path('app_logout') }}" class="btn btn-outline-danger mt-2">
                                    <i class="fas fa-sign-out-alt"></i> Se déconnecter
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ajouter Bootstrap CSS si ce n'est pas fait -->
    <style>
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2) !important;
        }
        .bg-primary {
            background-color: #0056b3 !important;
        }
    </style>
{% endblock %}
```

---

## 🔐 Étape 4 : Mettre à jour le `LoginSuccessHandler`

Modifiez le fichier : `src/Security/LoginSuccessHandler.php`

**Avant :**
```php
public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
{
    $user = $token->getUser();

    if (in_array('ROLE_ADMIN', $user->getRoles())) {
        return new RedirectResponse($this->router->generate('app_admin_acceuil'));
    }

    return new RedirectResponse($this->router->generate('app_home'));
}
```

**Après :**
```php
public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
{
    $user = $token->getUser();
    $roles = $user->getRoles();

    // Priorité : Admin > Médecin > Utilisateur
    if (in_array('ROLE_ADMIN', $roles)) {
        return new RedirectResponse($this->router->generate('app_admin_acceuil'));
    }
    
    if (in_array('ROLE_MEDECIN', $roles)) {
        return new RedirectResponse($this->router->generate('app_medecin_accueil'));
    }

    return new RedirectResponse($this->router->generate('app_home'));
}
```

---

## 🛡️ Étape 5 : Configurer la Sécurité (optionnel)

Le fichier `config/packages/security.yaml` peut être amélioré pour mieux sécuriser l'accès médecin.

**Fichier :** `config/packages/security.yaml`

Ajoutez une section dans `access_control` si vous voulez restricter l'accès à la route `/medecin` :

```yaml
access_control:
    - { path: ^/login, roles: PUBLIC_ACCESS }
    - { path: ^/logout, roles: PUBLIC_ACCESS }
    - { path: ^/admin, roles: ROLE_ADMIN }
    - { path: ^/medecin, roles: ROLE_MEDECIN }
    # - { path: ^/profile, roles: ROLE_USER }
```

**Note :** Comment l'attribut `#[IsGranted('ROLE_MEDECIN')]` est présent dans le contrôleur, cette config est optionnelle.

---

## 📦 Étape 6 : Créer un Utilisateur Médecin via la Base de Données

### Option A : Via l'Admin EasyAdmin (si disponible)

1. Allez à http://localhost:8000/admin
2. Créez un **Login** avec un email et mot de passe
3. Créez un **Utilisateur** associé au Login
4. Créez un **Profil** avec le rôle `medecin` pour cet utilisateur

### Option B : Via SQL

```sql
-- 1. Créer le Login
INSERT INTO login (mail, password) VALUES (
    'medecin@hopital.fr',
    '$2y$13$...' -- hash bcrypt du mot de passe
);

-- 2. Récupérer l'ID du Login fraichement créé (ex: id_login = 5)
-- 3. Créer l'Utilisateur
INSERT INTO utilisateur (nom, prenom, ville_res, cp, id_login) VALUES (
    'Dupont',
    'Jean',
    'Paris',
    '75001',
    5
);

-- 4. Récupérer l'ID de l'Utilisateur (ex: id_utilisateur = 10)
-- 5. Créer le Profil avec le rôle 'medecin'
INSERT INTO profil (role, id_utilisateur) VALUES ('medecin', 10);
```

### Option C : Via Commande PHP (le plus recommandé)

Créez une commande : `src/Command/CreateMedecinCommand.php`

```php
<?php

namespace App\Command;

use App\Entity\Login;
use App\Entity\Utilisateur;
use App\Entity\Profil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-medecin',
    description: 'Crée un utilisateur médecin'
)]
class CreateMedecinCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Créer le Login
        $login = new Login();
        $login->setMail('medecin@hopital.fr');
        $hashedPassword = $this->passwordHasher->hashPassword(
            $login,
            'password123' // À changer !
        );
        $login->setPassword($hashedPassword);

        $this->entityManager->persist($login);
        $this->entityManager->flush();

        // Créer l'Utilisateur
        $utilisateur = new Utilisateur();
        $utilisateur->setNom('Dupont');
        $utilisateur->setPrenom('Jean');
        $utilisateur->setVilleRes('Paris');
        $utilisateur->setCP('75001');
        $utilisateur->setLogin($login);

        $this->entityManager->persist($utilisateur);
        $this->entityManager->flush();

        // Créer le Profil Médecin
        $profil = new Profil();
        $profil->setRole('medecin');
        $profil->setUtilisateur($utilisateur);

        $this->entityManager->persist($profil);
        $this->entityManager->flush();

        $output->writeln('✅ Médecin créé avec succès : medecin@hopital.fr');
        return Command::SUCCESS;
    }
}
```

Ensuite, exécutez :
```bash
php bin/console app:create-medecin
```

---

## 🧪 Étape 7 : Tester la Connexion

1. **Allez à :** http://localhost:8000/login

2. **Connectez-vous avec :**
   - Email : `medecin@hopital.fr`
   - Mot de passe : `password123`

3. **Vous devriez être redirigé vers :** http://localhost:8000/medecin

4. **La page d'accueil du médecin doit s'afficher** avec son prénom, nom et les options disponibles.

---

## 📋 Checklist de Vérification

- [ ] Méthode `getRoles()` ajoutée à `Login.php`
- [ ] Contrôleur `MedecinController.php` créé
- [ ] Template `templates/medecin/accueil.html.twig` créé
- [ ] `LoginSuccessHandler.php` mis à jour
- [ ] Utilisateur médecin créé en base de données
- [ ] Connexion et redirection testées
- [ ] Page médecin accessible et fonctionnelle

---

## 🚀 Étapes Suivantes (Optionnel)

Après avoir réussi l'implémentation de base, vous pouvez :

1. **Créer les templates pour les pages médecin :**
   - `templates/medecin/patients.html.twig` - liste des patients
   - `templates/medecin/dossiers.html.twig` - gestion des dossiers

2. **Ajouter des permissions :**
   - Voir seulement les patients du médecin connecté
   - Modifier les notes médicales

3. **Améliorer le design :**
   - Ajouter une barre de navigation
   - Utiliser Bootstrap ou Tailwind

4. **Ajouter des fonctionnalités :**
   - Statistiques du médecin
   - Graphiques de patients
   - Export de données

---

## 🆘 Dépannage

### Erreur : "The method getRoles does not exist"
→ Ajoutez la méthode `getRoles()` à l'entité `Login.php`

### Erreur : "Route 'app_medecin_accueil' does not exist"
→ Vérifiez que le contrôleur `MedecinController.php` existe et que le serveur a été redémarré

### Redirection vers page accueil au lieu de médecin
→ Vérifiez que le `LoginSuccessHandler` a été mis à jour correctement

### Profil 'medecin' introuvable
→ Vérifiez que le profil existe en base de données (case-sensitive)

---

## 📚 Ressources Utiles

- [Documentation Symfony Security](https://symfony.com/doc/current/security.html)
- [Attribute Routing](https://symfony.com/doc/current/routing.html#creating-routes-as-attributes)
- [Entity Relations](https://symfony.com/doc/current/doctrine.html)

