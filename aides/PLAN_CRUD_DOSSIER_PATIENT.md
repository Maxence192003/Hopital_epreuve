# PLAN COMPLET : CRUD Dossier Patient - Étape par Étape

## 📋 Vue d'ensemble générale

On va créer un **CRUD Dossier Patient** identique au structure du CRUD Patients existant.

**CRUD = Create (créer), Read (voir), Update (modifier), Delete (supprimer)**

---

## 🎯 OBJECTIF FINAL

Quand l'utilisateur clique sur **"Dossiers Patients"** dans l'accueil, il arrive sur une **page CRUD complète** avec :
- ✅ Liste de tous les dossiers
- ✅ Bouton créer dossier
- ✅ Bouton modifier un dossier
- ✅ Bouton voir un dossier
- ✅ Bouton supprimer un dossier

---

## 📁 Structure à créer

```
src/Controller/Medecin/
├── DossierPatientCrudController.php    ← NOUVEAU (contrôleur avec 5 actions)

templates/medecin/
├── dossiers_liste.html.twig            ← NOUVEAU (liste des dossiers)
├── dossiers_form.html.twig             ← NOUVEAU (créer dossier)
├── dossiers_modifier.html.twig         ← NOUVEAU (modifier dossier)
├── dossiers_voir.html.twig             ← NOUVEAU (voir détails)
```

**Fichiers à MODIFIER :**
- `templates/home/medecin/accueil.html.twig` - Link vers `/medecin/dossiers`

---

## 🛠️ ÉTAPE 1 : Créer le Contrôleur

**QU'EST-CE ?** Un fichier PHP qui gère la logique métier

**POURQUOI ?** C'est lui qui récupère les données de la BD et les envoie aux templates

**COMMENT ?** 
- On crée `DossierPatientCrudController.php` 
- On ajoute 5 actions : `liste()`, `voir()`, `creer()`, `modifier()`, `supprimer()`
- Chaque action récupère les données et les envoie aux templates

**ACTIONS DÉTAILLÉES :**
```
liste()      → GET /medecin/dossiers → affiche tous les dossiers
voir()       → GET /medecin/dossiers/{id} → affiche détails 1 dossier
creer()      → GET + POST /medecin/dossiers/creer → forme + enregistre
modifier()   → GET + POST /medecin/dossiers/{id}/modifier → modifie
supprimer()  → POST /medecin/dossiers/{id}/supprimer → supprime
```

---

## 🎨 ÉTAPE 2 : Créer les Templates

**QU'EST-CE ?** Des fichiers HTML + Twig pour afficher les données

**POURQUOI ?** C'est l'interface que l'utilisateur voit

**COMMENT ?**
- `dossiers_liste.html.twig` - Tableau Bootstrap avec la liste + boutons
- `dossiers_form.html.twig` - Formulaire pour créer (même style que patients)
- `dossiers_modifier.html.twig` - Formulaire pour modifier
- `dossiers_voir.html.twig` - Page détails du dossier

**STYLE :** Identique aux templates patients (même layout, mêmes couleurs)

---

## 🔗 ÉTAPE 3 : Mettre à jour les liens

**QU'EST-CE ?** Ajouter le lien d'accès depuis l'accueil

**POURQUOI ?** Pour que l'utilisateur puisse accéder au CRUD

**COMMENT ?**
1. Modifier `accueil.html.twig` - Line 48 change `<a href="#"` en `<a href="{{ path('medecin_dossiers_liste') }}"`

---

## 📊 Comparaison avec le CRUD Patients

| Aspect | Patients | Dossiers Patients |
|--------|----------|------------------|
| Controller | `PatientsFormController.php` | `DossierPatientCrudController.php` |
| Route | `/medecin/patients/*` | `/medecin/dossiers/*` |
| Entity | `Utilisateur` | `DossierPatient` |
| Champs | nom, prenom, ville, CP, email | dateNaissance, etatGreffe, utilisateur |
| Liste | tous les patients | tous les dossiers |

---

## ⚠️ Pièges à éviter

1. ❌ Ne pas utiliser les mêmes routes que patients
2. ❌ Ne pas oublier les validations 
3. ❌ Ne pas mettre un style différent des patients
4. ❌ Ne pas oublier les flash messages (succès/erreur)

---

## ✅ Checklist d'exécution

- [ ] **ÉTAPE 1** : Créer contrôleur
- [ ] **ÉTAPE 2** : Créer template `dossiers_liste.html.twig`
- [ ] **ÉTAPE 3** : Créer template `dossiers_form.html.twig`
- [ ] **ÉTAPE 4** : Créer template `dossiers_modifier.html.twig`
- [ ] **ÉTAPE 5** : Créer template `dossiers_voir.html.twig`
- [ ] **ÉTAPE 6** : Mettre à jour accueil.html.twig

---

## ⚡ IMPLÉMENTATION FINALE - VERSION COMPLÈTE

**Implémentation complète du CRUD pour DossierPatient ✅**

### 📁 Fichiers Créés

**Contrôleur :**
- `src/Controller/Medecin/DossierPatientCrudController.php` (9 actions)

**Templates (7 fichiers) :**
- `templates/medecin/dossiers/index.html.twig` 
- `templates/medecin/dossiers/voir.html.twig`
- `templates/medecin/dossiers/modifier.html.twig`
- `templates/medecin/dossiers/creer_liste.html.twig`
- `templates/medecin/dossiers/creer.html.twig`
- `templates/medecin/dossiers/sans_greffe_liste.html.twig`
- `templates/medecin/dossiers/ajouter_greffe.html.twig`

**Repositories (Méthodes Ajoutées) :**
- `UtilisateurRepository::findByRoleName()`
- `DossierPatientRepository::findByUtilisateur()`

### 🎯 Fonctionnalités

**SECTION 1 : Dossiers Patients**
- Liste des patients avec dossier
- Bouton "Voir" → Affiche tous les détails (utilisateur, greffes, notes médicales)
- Bouton "Modifier" → Change l'état de greffe (dropdown: En attente / Greffer / Bon / Mauvais)

**SECTION 2 : Créer Dossier Patient**
- Liste des patients SANS dossier
- Formulaire : Date de naissance + État de greffe
- Crée automatiquement le DossierPatient

**SECTION 3 : Dossiers Sans Greffe**
- Liste des dossiers SANS greffe associée
- Bouton "Ajouter Greffe"
- Formulaire : Date greffe + Notes

### 🔗 Lien d'Accès
- Bouton "Dossiers Patients" dans accueil.html.twig
- Route : `medecin_dossiers_index`

📝 **Documentation complète :** [DOCUMENTATION_CRUD_DOSSIER_PATIENT.md](DOCUMENTATION_CRUD_DOSSIER_PATIENT.md)

---
