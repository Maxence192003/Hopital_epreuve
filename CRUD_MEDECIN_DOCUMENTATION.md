# Documentation CRUD Médecin - Gestion des Patients

## Table des matières
1. [Vue d'ensemble](#vue-densemble)
2. [Architecture](#architecture)
3. [Fichiers créés](#fichiers-créés)
4. [Explication détaillée](#explication-détaillée)
5. [Fonctionnement](#fonctionnement)
6. [Comment modifier](#comment-modifier)
7. [Troubleshooting](#troubleshooting)

---

## Vue d'ensemble

Ce CRUD permet aux **médecins** de gérer une liste de **patients**. Les fonctionnalités incluent :
- ✅ Voir la liste de tous les patients (avec rôle `ROLE_PATIENT`)
- ✅ Voir les détails d'un patient
- ✅ Créer un nouveau patient
- ✅ Modifier un patient existant
- ✅ Supprimer un patient

### Points clés de sécurité
- Seuls les utilisateurs avec `ROLE_MEDECIN` peuvent accéder
- Les médecins ne voient **que** les patients (utilisateurs avec `ROLE_PATIENT`)
- Les patients créés par un médecin ont automatiquement le rôle `ROLE_PATIENT`

---

## Architecture

```
Médecin (tableau de bord)
    ↓
    ├─→ [Lien] Patients
    │       ↓
    │   PatientsFormController@liste
    │       ↓
    │   patients_list.html.twig (Liste des patients)
    │       ├─→ [Voir] → patients_voir.html.twig
    │       ├─→ [Modifier] → patients_modifier.html.twig
    │       └─→ [Supprimer] → DELETE
    │
    └─→ [Lien] Ajouter Patient
            ↓
        PatientsFormController@creer
            ↓
        patients_form.html.twig (Formulaire création)
```

---

## Fichiers créés

### 1. **Contrôleur** : `src/Controller/Medecin/PatientsFormController.php`
Gère toute la logique CRUD pour les patients.

### 2. **Layout** : `templates/medecin/layout.html.twig`
Template de base pour toutes les pages médecin (navbar, sidebar, styles).

### 3. **Templates de vue**
- `templates/medecin/patients_list.html.twig` - Liste des patients
- `templates/medecin/patients_voir.html.twig` - Détails d'un patient
- `templates/medecin/patients_form.html.twig` - Formulaire création
- `templates/medecin/patients_modifier.html.twig` - Formulaire modification

### 4. **Configuration**
- `config/packages/twig.yaml` - Ajout du namespace `@medecin`
- `templates/home/medecin/accueil.html.twig` - Lien vers les patients

---

## Explication détaillée

### 📋 Le Contrôleur (`PatientsFormController.php`)

```php
#[IsGranted('ROLE_MEDECIN')]  // ← Restriction d'accès
#[Route('/medecin/patients')]  // ← Préfixe de route
class PatientsFormController extends AbstractController
```

**Explication** : 
- `@IsGranted('ROLE_MEDECIN')` = seuls les médecins peuvent accéder
- `@Route('/medecin/patients')` = toutes les routes commencent par `/medecin/patients/`

#### Méthode `liste()`

```php
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
```

**Route complète** : `/medecin/patients/liste`  
**Nom de route** : `medecin_patients_liste`  
**Explication** :
1. On récupère tous les `Utilisateur`
2. On **joint** avec les `Profil` associés
3. On **filtre** pour avoir seulement ceux avec `ROLE_PATIENT`
4. On **trie** par Nom (A→Z)
5. On passe les patients au template

#### Méthode `creer()`

```php
#[Route('/creer', name: 'medecin_patients_creer', methods: ['GET', 'POST'])]
public function creer(Request $request): Response
{
    if ($request->isMethod('POST')) {
        // 1. Récupérer les données du formulaire
        $nom = $request->request->get('nom');
        $prenom = $request->request->get('prenom');
        // ... etc
        
        // 2. Créer et persister le Login
        $login = new Login();
        $login->setMail($mail);
        $hashedPassword = $this->hasherFactory
            ->getPasswordHasher(Login::class)
            ->hash($password);  // ← Hash du mot de passe
        $login->setPassword($hashedPassword);
        $this->entityManager->persist($login);
        $this->entityManager->flush();
        
        // 3. Créer et persister l'Utilisateur
        $utilisateur = new Utilisateur();
        $utilisateur->setNom($nom);
        $utilisateur->setPrenom($prenom);
        $utilisateur->setLogin($login);
        $this->entityManager->persist($utilisateur);
        $this->entityManager->flush();
        
        // 4. Créer et persister le Profil (ROLE_PATIENT forcé)
        $profil = new Profil();
        $profil->setRole('ROLE_PATIENT');  // ← TOUJOURS ROLE_PATIENT
        $profil->setUtilisateur($utilisateur);
        $this->entityManager->persist($profil);
        $this->entityManager->flush();
        
        return $this->redirectToRoute('medecin_patients_liste');
    }
    
    return $this->render('medecin/patients_form.html.twig');
}
```

**Route complète** : `/medecin/patients/creer`  
**Méthodes HTTP** : GET (affiche le formulaire) et POST (traite la soumission)  
**Explication du flux** :
1. Si c'est un POST, on récupère les données
2. On crée un `Login` avec le mot de passe **hashé**
3. On crée un `Utilisateur` avec les infos civiles et le Login
4. On crée un `Profil` avec le rôle `ROLE_PATIENT`
5. On redirige vers la liste

**Points importants** :
- Le mot de passe est **toujours hashé** avec `PasswordHasherFactoryInterface`
- Le rôle est **forcé à ROLE_PATIENT** (le médecin ne peut pas changer)

#### Méthode `voir()`

```php
#[Route('/{id}/voir', name: 'medecin_patients_voir', methods: ['GET'])]
public function voir($id): Response
{
    $patient = $this->entityManager->getRepository(Utilisateur::class)->find($id);
    
    if (!$patient) {
        throw $this->createNotFoundException('Patient introuvable');
    }
    
    // Vérifier que c'est bien un patient
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
```

**Route complète** : `/medecin/patients/{id}/voir`  
**Explication** :
1. Récupérer l'utilisateur par son ID
2. Vérifier qu'il existe (sinon 404)
3. Vérifier que c'est avec le rôle `ROLE_PATIENT` (sinon 403)
4. Afficher les détails

**Sécurité** : On vérifie vraiment que c'est un patient avant d'afficher

#### Méthode `modifier()`

```php
#[Route('/{id}/modifier', name: 'medecin_patients_modifier', methods: ['GET', 'POST'])]
public function modifier($id, Request $request): Response
{
    // ... Vérifications d'existence et de rôle ...
    
    if ($request->isMethod('POST')) {
        // Mettre à jour les infos civiles
        $patient->setNom($request->request->get('nom'));
        $patient->setPrenom($request->request->get('prenom'));
        // ...
        
        // Mettre à jour le Login
        $login = $patient->getLogin();
        $login->setMail($request->request->get('mail'));
        
        // Mettre à jour le mot de passe SI fourni
        $password = $request->request->get('password');
        if ($password) {
            // Vérifier que ce n'est pas déjà un hash (les hash bcrypt commencent par $2)
            if (!str_starts_with($password, '$2')) {
                $hashedPassword = $this->hasherFactory
                    ->getPasswordHasher(Login::class)
                    ->hash($password);
                $login->setPassword($hashedPassword);
            }
        }
        
        $this->entityManager->flush();
        return $this->redirectToRoute('medecin_patients_liste');
    }
    
    return $this->render('medecin/patients_modifier.html.twig', [
        'patient' => $patient
    ]);
}
```

**Route complète** : `/medecin/patients/{id}/modifier`  
**Explication** :
1. Vérifier le patient
2. Si POST : mettre à jour tous les champs
3. Si le mot de passe est fourni ET n'est pas déjà un hash, le hasher
4. Enregistrer et rediriger

**Point clé** : Le check `!str_starts_with($password, '$2')` empêche de hasher deux fois un mot de passe

#### Méthode `supprimer()`

```php
#[Route('/{id}/supprimer', name: 'medecin_patients_supprimer', methods: ['POST'])]
public function supprimer($id, Request $request): Response
{
    // ... Vérifications ...
    
    // Supprimer le Login associé
    $login = $patient->getLogin();
    if ($login) {
        $this->entityManager->remove($login);
    }
    
    // Supprimer le patient (les Profils se suppriment en cascade)
    $this->entityManager->remove($patient);
    $this->entityManager->flush();
    
    return $this->redirectToRoute('medecin_patients_liste');
}
```

**Route complète** : `/medecin/patients/{id}/supprimer`  
**Méthode HTTP** : POST uniquement (pour éviter les suppressions accidentelles via GET)  
**Explication** :
1. Vérifier le patient
2. Supprimer le Login
3. Supprimer le Utilisateur (les Profils s'en vont en cascade grâce à `cascade: ['remove']`)
4. Rediriger

---

### 🎨 Le Layout (`layout.html.twig`)

```html
{% extends 'base.html.twig' %}
```

**Explication** :
- Hérite de `base.html.twig` (qui a le doctype HTML, head, etc)
- Ajoute une navbar Bootstrap
- Ajoute une sidebar avec menu de navigation
- Contient tous les styles CSS

**Structure** :
```
<nav></nav>           ← Barre supérieure avec logo et bouton déconnexion
<div class="row">
  <sidebar></sidebar> ← Menu latéral avec 3 liens
  <main></main>       ← Contenu principal (bloc {% block main %})
</div>
```

**Menu latéral** :
```
🏠 Tableau de bord   → app_medecin_accueil
👥 Voir Patients     → medecin_patients_liste
➕ Ajouter Patient   → medecin_patients_creer
```

---

### 📄 Les Templates

#### `patients_list.html.twig`
Affiche un **tableau** avec tous les patients et les actions.

```html
{% extends '@medecin/layout.html.twig' %}
```

**Colonnes du tableau** :
- Nom
- Prénom
- Ville
- Code Postal
- Email
- Actions (Voir 👁️, Modifier ✏️, Supprimer 🗑️)

#### `patients_voir.html.twig`
Affiche les **détails complets** d'un patient en **cartes**.

**Sections** :
- Informations Civiles (Nom, Prénom, Ville, CP)
- Authentification (Email)
- Rôle (Badge)

#### `patients_form.html.twig`
Formulaire de **création** d'un patient.

Champs :
- Nom (required)
- Prénom (required)
- Ville (required)
- Code Postal (required)
- Email (required, email)
- Mot de passe (required, min 6 caractères)

**Important** : Pas de sélection de rôle, c'est forcé à ROLE_PATIENT

#### `patients_modifier.html.twig`
Formulaire de **modification** d'un patient.

**Différences avec le formulaire de création** :
- Les champs sont pré-remplis avec les valeurs actuelles
- Le mot de passe est optionnel (texte d'aide : "Laissez vide pour conserver")

---

## Fonctionnement

### 1. Un médecin accède au tableau de bord
```
GET /medecin
→ MedecinController::index()
→ templates/home/medecin/accueil.html.twig
```

### 2. Le médecin clique sur "Patients"
```
GET /medecin/patients/liste
→ PatientsFormController::liste()
→ Récupère les utilisateurs avec ROLE_PATIENT
→ templates/medecin/patients_list.html.twig
```

### 3. Le médecin crée un patient
```
GET /medecin/patients/creer   (affiche le formulaire)
POST /medecin/patients/creer  (reçoit les données)
→ PatientsFormController::creer()
→ Crée Login + Utilisateur + Profil(ROLE_PATIENT)
→ Redirige vers la liste
```

### 4. Le médecin modifie un patient
```
GET /medecin/patients/{id}/modifier       (affiche le formulaire)
POST /medecin/patients/{id}/modifier      (reçoit les données)
→ PatientsFormController::modifier()
→ Met à jour Login + Utilisateur
→ Redirige vers la liste
```

### 5. Le médecin supprime un patient
```
POST /medecin/patients/{id}/supprimer
→ PatientsFormController::supprimer()
→ Supprime Login + Utilisateur (+ Profils en cascade)
→ Redirige vers la liste
```

---

## Comment modifier

### ❓ Ajouter un champ au formulaire

**Exemple** : Ajouter un numéro de téléphone

#### 1. Ajouter le champ à l'entité `Utilisateur`

```php
// src/Entity/Utilisateur.php

#[ORM\Column(length: 20, nullable: true)]
private ?string $Telephone = null;

public function getTelephone(): ?string
{
    return $this->Telephone;
}

public function setTelephone(?string $Telephone): static
{
    $this->Telephone = $Telephone;
    return $this;
}
```

#### 2. Créer une migration

```bash
docker compose exec php php bin/console make:migration
docker compose exec php php bin/console doctrine:migrations:migrate
```

#### 3. Ajouter le champ au contrôleur

```php
// src/Controller/Medecin/PatientsFormController.php

// Dans creer()
$telephone = $request->request->get('telephone');
$utilisateur->setTelephone($telephone);

// Dans modifier()
$patient->setTelephone($request->request->get('telephone'));
```

#### 4. Ajouter le champ aux templates

```html
<!-- templates/medecin/patients_form.html.twig -->
<div class="form-group mb-3">
    <label for="telephone" class="form-label">Téléphone</label>
    <input type="tel" id="telephone" name="telephone" class="form-control">
</div>

<!-- templates/medecin/patients_modifier.html.twig -->
<div class="form-group mb-3">
    <label for="telephone" class="form-label">Téléphone</label>
    <input type="tel" id="telephone" name="telephone" class="form-control" value="{{ patient.telephone }}">
</div>

<!-- templates/medecin/patients_voir.html.twig -->
<strong>Téléphone:</strong> {{ patient.telephone }}

<!-- templates/medecin/patients_list.html.twig -->
<th>Téléphone</th>
<!-- et dans le tbody -->
<td>{{ patient.telephone }}</td>
```

### ❓ Changer la restriction de rôle

Si vous voulez que les médecins gèrent autre chose que des patients :

```php
// src/Controller/Medecin/PatientsFormController.php

// Remplacer `ROLE_PATIENT` par le rôle souhaité partout

// Dans liste()
->where('p.Role = :role')
->setParameter('role', 'VOTRE_ROLE')  // ← Changer ici

// Dans creer()
$profil->setRole('VOTRE_ROLE');  // ← Et ici
```

### ❓ Ajouter une colonne au tableau

```html
<!-- templates/medecin/patients_list.html.twig -->

<!-- Dans le thead -->
<th>Colonne supplémentaire</th>

<!-- Dans le tbody (la boucle) -->
<td>{{ patient.propriete }}</td>
```

### ❓ Changer l'URL de base

Si vous voulez changer `/medecin/patients` en autre chose :

```php
// src/Controller/Medecin/PatientsFormController.php

#[Route('/autre-url')]  // ← Changer ici
class PatientsFormController extends AbstractController
```

**⚠️ Important** : Vous devrez aussi mettre à jour les routes dans :
- Le template d'accueil médecin
- Le layout médecin
- Tous les `path()` dans les templates

---

## Troubleshooting

### ❌ Erreur : "Route 'medecin_patients_liste' not found"

**Cause** : Le cache n'est pas à jour

**Solution** :
```bash
docker compose exec php php bin/console cache:clear
```

### ❌ Erreur : "Class has no field named 'role'"

**Cause** : Vous avez écrit `p.role` au lieu de `p.Role` (la propriété a un R majuscule)

**Solution** : Vérifier la casse des champs dans :
- Les requêtes DQL
- Les appels à `->setRole()` et `->getRole()`

### ❌ Les patients ne s'affichent pas

**Cause possible 1** : Pas de patients avec `ROLE_PATIENT` en base de données

**Cause possible 2** : La requête DQL filtre mal

**Solution** :
```bash
# Vérifier les patients en base
docker compose exec php php bin/console doctrine:query:sql "SELECT * FROM utilisateur"
docker compose exec php php bin/console doctrine:query:sql "SELECT * FROM profil"
```

### ❌ Le mot de passe n'est pas accepté à la connexion

**Cause** : Pas de hash ou mauvais hash

**Solution** : Vérifier que `PasswordHasherFactoryInterface` est utilisé et que le mot de passe est bien hashé avant de persister

### ❌ Accès refusé (403)

**Cause** : L'utilisateur n'a pas `ROLE_MEDECIN`

**Solution** : Vérifier dans la base de données que l'utilisateur a un Profil avec le rôle `ROLE_MEDECIN`

---

## Récapitulatif des routes

| Route | Méthode | Contrôleur | Template | Description |
|-------|---------|-----------|----------|------------|
| `/medecin/patients/liste` | GET | `liste()` | `patients_list.html.twig` | Liste des patients |
| `/medecin/patients/creer` | GET | `creer()` | `patients_form.html.twig` | Affiche le formulaire |
| `/medecin/patients/creer` | POST | `creer()` | - | Traite la création |
| `/medecin/patients/{id}/voir` | GET | `voir()` | `patients_voir.html.twig` | Détails du patient |
| `/medecin/patients/{id}/modifier` | GET | `modifier()` | `patients_modifier.html.twig` | Affiche le formulaire |
| `/medecin/patients/{id}/modifier` | POST | `modifier()` | - | Traite la modification |
| `/medecin/patients/{id}/supprimer` | POST | `supprimer()` | - | Supprime le patient |

---

## Résumé

✅ Le CRUD médecin est **isolé** du CRUD admin  
✅ Les médecins ne voient que les **patients** (rôle ROLE_PATIENT)  
✅ La création force le rôle **ROLE_PATIENT**  
✅ Le mot de passe est **toujours hashé**  
✅ La suppression utilise une **cascade delete**  
✅ Chaque action a sa **propre route nommée**  
✅ Les **templates héritent** du layout médecin  
✅ La **sécurité** est appliquée partout (`@IsGranted`, vérifications)  

---

**Dernière mise à jour** : 13 mars 2026
