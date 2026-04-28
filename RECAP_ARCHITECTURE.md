# 🏥 Récapitulatif - Système de Gestion Hospitalier

## 📋 Vue d'ensemble

C'est une **application web Symfony** pour gérer les dossiers patients et les greffes dans un hôpital. 
- **Framework**: Symfony 7+ (PHP)
- **Base de données**: MySQL/MariaDB (via Docker)
- **Admin**: EasyAdmin Bundle
- **Authentification**: Formulaire email/mot de passe
- **Frontend**: Twig (templates HTML) + JavaScript

---

## 🗂️ Modèle de Données (Entités)

### **1. Login** 🔐
- **Email** (unique, validé)
- **Mot de passe** (min 6 caractères, hashé)
- Relation: `1 Login → N Utilisateurs`
- Implémente l'interface `UserInterface` pour Symfony

### **2. Profil** 👤
- **Role** (ex: "Admin", "Medecin", "Patient")
- Relation: `1 Profil → N Utilisateurs`

### **3. Utilisateur** 👥
- **Nom**, **Prénom**
- **Ville de résidence**, **Code postal**
- Relations:
  - `1 Utilisateur → 1 Login` (compte d'accès)
  - `1 Utilisateur → 1 Profil` (rôle)
  - `1 Utilisateur → 1 DossierPatient` (dossier médical)

### **4. DossierPatient** 📑
- **ID** (identifiant unique)
- **Date de naissance**
- **Etat greffe** (ex: "En attente", "Greffé", "Rejeté")
- Relations:
  - `1 DossierPatient → 1 Utilisateur`
  - `1 DossierPatient → N NoteMedicales`
  - `1 DossierPatient → N Greffes`

### **5. NoteMedical** 📝
- **ID**, **Texte** de la note
- **Date/heure** de création
- Relation: `N NoteMedical → 1 DossierPatient`
- Permet aux médecins de documenter le suivi

### **6. Greffe** 🏥
- **ID**, **Date de greffe**
- **Note greffe** (post-transplantation)
- **Note donneur** (informations du donneur)
- Relation: `N Greffe → 1 DossierPatient`

```
Schéma simplifié des relations:

Login (1) ──→ (N) Utilisateur ←─ (1) DossierPatient
              ↓                      ↓
           Profil               NoteMedical (1..N)
                                     ↓
                                  Greffe (1..N)
```

---

## 🔐 Authentification & Sécurité

### **Flux de connexion**
1. Utilisateur accède `/login`
2. Remplit le formulaire (Email + Mot de passe)
3. Symfony valide les credentials via `LoginUserProvider`
4. Si valide → `LoginSuccessHandler` redirige selon son rôle
5. Si invalide → Message d'erreur

### **Données sécurisées**
- Mot de passes **hasés** (algorithme auto Symfony)
- CSRF **activé** pour tous les formulaires
- Firewall protège les routes (`/login`, `/logout` = publique)

---

## 🎯 Flux de l'Application

### **Page d'accueil** (`/`)
- Affiche menu avec 4 sections informationnelles:
  - 📊 **Evaluation** (`/evaluation`)
  - 🏥 **Transplantation** (`/transplantation`)
  - ✅ **Suivi post-greffe** (`/suivi-post-greffe`)
  - 🔍 **Recherche** (`/recherche`)

### **Après connexion** (selon le profil)

#### **Admin**
- Accès **EasyAdmin** (`/admin`)
- Gère les entités:
  - ✅ Utilisateurs (créer, modifier, supprimer)
  - ✅ Logins (créer, modifier, supprimer)
  - ✅ Profils (rôles)
  - ✅ Dossiers patients (CRUD complet)
  - ✅ Greffes (CRUD complet)

#### **Médecin**
- Accès **interfaces dédiées** (`/medecin/`)
  - **Gestion dossiers patients**: afficher, modifier
  - **Gestion greffes**: créer/modifier notes de greffe
  - **Suivi médical**: ajouter notes medicales pour chaque patient

#### **Patient**
- Vue de son dossier personnel
- Consultation des greffes/notes medicales

---

## 🏗️ Architecture des Contrôleurs

### **Niveau 1: Public**
```
HomeController
├── / (home)
├── /evaluation
├── /transplantation
├── /suivi-post-greffe
└── /recherche

LoginController
├── /login (GET/POST)
└── /logout (GET)
```

### **Niveau 2: Admin** (`/admin`)
Via EasyAdmin (CRUD automatisé):
```
UtilisateurCrudController
LoginCrudController
ProfilCrudController
DossierPatientCrudController
GreffeCrudController
```

### **Niveau 3: Médecin** (`/medecin`)
```
DossierPatientCrudController    (afficher/modifier patients)
DossierGreffeController         (gérer greffes & notes)
PatientsFormController          (formulaires patients)
```

---

## 🔄 Workflow Médecin (Exemple)

```
1. Médecin se connecte
   ↓
2. Accède à la liste de ses patients
   ↓
3. Clique sur un patient → Voir dossier
   ↓
4. Peut:
   a) Ajouter une NOTE MEDICALE
   b) Consigner une GREFFE (date, notes)
   c) Mettre à jour l'ETAT GREFFE
   ↓
5. Les données sont persistées en BD
```

---

## 🗃️ Structure du Projet

```
Hopital_epreue/
├── src/
│   ├── Controller/
│   │   ├── HomeController.php
│   │   ├── LoginController.php
│   │   ├── PatientController.php
│   │   ├── MedecinController.php
│   │   ├── Admin/          (EasyAdmin CRUD)
│   │   └── Medecin/        (Routes médecin)
│   ├── Entity/             (6 entités)
│   ├── Repository/         (Accès BD)
│   ├── Security/           (Auth, UserProvider)
│   └── Kernel.php
├── config/
│   ├── packages/
│   │   ├── security.yaml   (authentification)
│   │   ├── easy_admin.yaml (config EasyAdmin)
│   │   └── ...
│   └── routes.yaml         (import routes)
├── templates/              (Twig HTML)
├── migrations/             (Versioning BD)
├── public/                 (index.php, assets)
└── composer.json           (dépendances)
```

---

## 🚀 Technologies Utilisées

| Composant | Technologie |
|-----------|------------|
| **Backend** | Symfony 7.x (PHP) |
| **Base de données** | MySQL/MariaDB 8+ |
| **ORM** | Doctrine |
| **Admin Panel** | EasyAdmin 4+ |
| **Frontend** | Twig, HTML5, CSS3 |
| **Frontend JS** | Stimulus.js (composants interactifs) |
| **Conteneurisation** | Docker + Docker Compose |
| **Tests** | PHPUnit |

---

## 🔗 Interactions Principales

```
Patient
  ↓
  └─→ Login (authentification)
      ↓
      └─→ Utilisateur (profil)
          ↓
          ├─→ Profil (rôle: Admin/Medecin/Patient)
          └─→ DossierPatient
              ├─→ Notes Medicales (suivi)
              └─→ Greffes (historique transplantations)
```

---

## ✨ Fonctionnalités Clés

✅ **Authentification** - Login sécurisé par rôle  
✅ **Gestion patients** - CRUD dossiers + infos personnelles  
✅ **Suivi médical** - Notes et observations par médecin  
✅ **Gestion greffes** - Historique complet (date, notes donneur/receveur)  
✅ **Admin Panel** - Interface complète EasyAdmin  
✅ **Validation** - Formulaires avec contraintes Symfony  
✅ **Sécurité** - CSRF, hachage mot de passe, authentification  

---

## 📊 Exemple: Parcours d'un Patient Greffé

```
1. Patient créé avec Login (Email + MDP)
   └─ Profil: "Patient"

2. Dossier patient créé
   └─ État: "En attente de greffe"
   └─ Date naissance: [enregistrée]

3. Médecin ajoute NOTE MEDICALE
   └─ "Compatibilité vérifiée, patient stable"
   └─ Date/heure: auto

4. Greffe effectuée
   └─ ID_greffe créée
   └─ Date_greffe: 15/04/2026
   └─ Note_donneur: "Donneur âgé 45ans, groupe O"
   └─ Note_greffe: "Transplantation réussie"
   └─ État du patient → "Greffé"

5. Suivi post-greffe
   └─ Médecin ajoute nouvelles NOTES
   └─ Monitoring de la greffe
```

---

**Créé le**: 3 avril 2026  
**Pour**: Présentation Épreuve Situation Professionnelle
