# Documentation CRUD Complet - Gestion des Dossiers Patients

## 🎯 Objectif

Créer un **CRUD complet** pour gérer les dossiers patients avec **3 sections** accessibles via un menu TAB :

1. **Dossiers Patients** → Voir les dossiers existants
2. **Créer Dossier Patient** → Créer un dossier pour un patient
3. **Dossiers Sans Greffe** → Ajouter une greffe aux dossiers

---

## 📋 Structure Implémentée

### Contrôleur Principal

**Fichier:** `src/Controller/Medecin/DossierPatientCrudController.php`

**Routes et Actions:**
```
GET  /medecin/dossiers/                      → index()           (Section 1 - Liste)
GET  /medecin/dossiers/{id}/voir             → voir()            (Section 1 - Détails)
GET  /medecin/dossiers/{id}/modifier         → modifier()        (Section 1 - Modifier état)
POST /medecin/dossiers/{id}/modifier         → modifier()        (Section 1 - Traiter)

GET  /medecin/dossiers/creer/liste           → sectionCreer()    (Section 2 - Liste patients)
GET  /medecin/dossiers/creer/{id}            → creer()           (Section 2 - Formulaire)
POST /medecin/dossiers/creer/{id}            → creer()           (Section 2 - Traiter)

GET  /medecin/dossiers/sans-greffe/liste     → sectionSansGreffe() (Section 3 - Liste)
GET  /medecin/dossiers/{id}/ajouter-greffe   → ajouterGreffe()     (Section 3 - Formulaire)
POST /medecin/dossiers/{id}/ajouter-greffe   → ajouterGreffe()     (Section 3 - Traiter)
```

---

## 📁 Templates Créés

**Dossier:** `templates/medecin/dossiers/`

### 1. `index.html.twig`
- Menu TAB en haut (3 sections)
- Tableau avec liste des dossiers
- Colonnes : Nom, Prénom, Date Naissance, État Greffe, Actions
- Boutons "Voir" et "Modifier"

### 2. `voir.html.twig`
- Affiche tous les détails du dossier
- **Informations Civiles :** Nom, Prénom, Ville, CP, Email
- **Informations Dossier :** Date Naissance, État Greffe
- **Greffes :** Liste de toutes les greffes avec dates et notes
- **Notes Médicales :** Liste de toutes les notes médicales

### 3. `modifier.html.twig`
- Formulaire pour modifier l'état de la greffe
- **Dropdown avec 4 options :** 
  - En attente
  - Greffer
  - Bon
  - Mauvais
- Informations patient en read-only

### 4. `creer_liste.html.twig`
- Menu TAB + inscription "Créer Dossier Patient"
- Tableau des patients SANS dossier
- Bouton "Créer Dossier" pour chaque patient

### 5. `creer.html.twig`
- Formulaire de création dossier
- Champs :
  - Nom, Prénom, Ville, CP (read-only de l'utilisateur)
  - **Date de Naissance** (optionnel)
  - **État de la Greffe** (optionnel, dropdown)
- Bouton "Créer le Dossier"

### 6. `sans_greffe_liste.html.twig`
- Menu TAB + inscription "Dossiers Sans Greffe"
- Tableau des dossiers sans greffe
- Bouton "Ajouter Greffe" pour chaque dossier

### 7. `ajouter_greffe.html.twig`
- Formulaire pour ajouter une greffe
- Champs :
  - **Date de Greffe** (requis, datetime-local)
  - **Note Greffe** (optionnel, textarea)
  - **Note Donneur** (optionnel, textarea)
- Informations patient en read-only
- Bouton "Ajouter la Greffe"

---

## 🔄 Flux Utilisateur

### Section 1 : Dossiers Patients
```
Clique "Dossiers Patients"
    ↓
Affiche liste de tous les dossiers (avec utilisateur)
    ↓
Clique "Voir" → Page détails complète
    ↓
Ou clique "Modifier" → Change l'état de greffe
    ↓
Sauvegarde + Redirection
```

### Section 2 : Créer Dossier
```
Clique "Créer Dossier Patient"
    ↓
Affiche liste des patients SANS dossier
    ↓
Clique "Créer Dossier" sur un patient
    ↓
Formulaire : Date naissance + État greffe
    ↓
Sauvegarde + Redirection
```

### Section 3 : Ajouter Greffe
```
Clique "Dossiers Sans Greffe"
    ↓
Affiche liste des dossiers SANS greffe
    ↓
Clique "Ajouter Greffe"
    ↓
Formulaire : Date greffe + Notes
    ↓
Sauvegarde + Redirection
```

---

## 🔌 Méthodes Ajoutées aux Repositories

### `UtilisateurRepository`
```php
findByRoleName(string $roleName): array
// Récupère tous les utilisateurs ayant un profil avec le rôle spécifié
// Utilisé pour trouver les patients avec ROLE_PATIENT
```

### `DossierPatientRepository`
```php
findByUtilisateur($utilisateur): ?DossierPatient
// Récupère le DossierPatient d'un utilisateur
// Retourne null si n'existe pas
```

---

## 💾 Modèle de Données Utilisé

### DossierPatient
```php
- id_dossier_patient : string (PK)
- Date_naissance : DateTime (nullable)
- Etat_greffe : string (nullable)
- utilisateur : Utilisateur (OneToOne)
- greffes : Collection<Greffe>
- notesMedicales : Collection<NoteMedical>
```

### Greffe
```php
- id_greffe : string (PK)
- Date_greffe : DateTime (nullable)
- Note_greffe : string (nullable)
- Note_donneur : string (nullable)
- dossierPatient : DossierPatient (ManyToOne)
```

### Utilisateur
```php
- id_utilisateur : int (PK)
- nom, prenom : string
- villeRes, CP : string
- dossierPatient : DossierPatient (OneToOne)
- profil : Profil (ManyToOne) → pour ROLE_PATIENT
- login : Login (ManyToOne)
```

---

## 🔐 Sécurité

- **Authentification :** Annotation `#[IsGranted('ROLE_MEDECIN')]`
- **Vérifications métier :**
  - Vérifier que le patient n'a pas déjà un dossier
  - Vérifier que le dossier a bien un utilisateur associé
  - Vérifier que le dossier n'a pas déjà une greffe

---

## ✅ Génération des IDs

Les IDs sont générés avec **uniqid()** :
- DossierPatient : `DOSS_` + uniqid
- Greffe : `GREFFE_` + uniqid

```php
$dossier->setIdDossierPatient(uniqid('DOSS_'));
$greffe->setIdGreffe(uniqid('GREFFE_'));
```

---

## 📝 Messages Flash

- **Success :** "Dossier patient créé avec succès", "Greffe ajoutée avec succès", etc.
- **Error :** "Ce dossier n'a pas d'utilisateur associé", "Cet utilisateur a déjà un dossier", etc.

---

## 🎨 Design UI

- **Héritage :** Tous les templates héritent de `@medecin/layout.html.twig`
- **Menu TAB :** Navigation entre 3 sections via Bootstrap nav-tabs
- **Formulaires :** Bootstrap form-control, form-label
- **Boutons :** Couleurs cohérentes (success, warning, danger, info)
- **Tableaux :** Bootstrap table-striped, table-hover, table-dark

---

## 🚀 Tests À Effectuer

1. **Section 1 - Voir liste :**
   - Vérifier que tous les dossiers avec utilisateur s'affichent

2. **Section 1 - Voir détails :**
   - Cliquer "Voir" → Afficher toutes les infos complètes

3. **Section 1 - Modifier :**
   - Cliquer "Modifier" → Change état greffe → Vérifie en BD

4. **Section 2 - Créer :**
   - Affiche patients sans dossier
   - Cliquer "Créer" → Formulaire → Sauvegarde → Redirection

5. **Section 3 - Ajouter Greffe :**
   - Affiche dossiers sans greffe
   - Cliquer "Ajouter Greffe" → Formulaire → Sauvegarde → Redirection

---

## 📊 Accès

**Point d'entrée :** Bouton "Dossiers Patients" dans accueil.html.twig
- Route : `medecin_dossiers_index`
- Lien : `{{ path('medecin_dossiers_index') }}`

---

**Statut :** ✅ **Implémentation Complète**
