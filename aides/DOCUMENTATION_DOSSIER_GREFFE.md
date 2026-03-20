# Documentation CRUD Gestion État de Greffe - Médecin

## 🎯 Objectif

Permettre aux médecins de consulter la liste de tous les patients ayant un dossier, et de modifier facilement l'état de la greffe pour chaque patient.

---

## 📋 Structure Implémentée

### Fichiers Créés

#### 1️⃣ Contrôleur : `src/Controller/Medecin/DossierGreffeController.php`

**Rôle:** Gérer la logique métier pour l'affichage et modification de l'état de greffe

**Routes:**
- `GET /medecin/dossiers-greffe/` → `liste()` - Affiche tous les dossiers
- `GET /medecin/dossiers-greffe/{id}/modifier` → `modifier()` - Affiche le formulaire
- `POST /medecin/dossiers-greffe/{id}/modifier` → `modifier()` - Traite la modification

**Détails des Méthodes:**

**`liste()` - Action liste**
```php
- Récupère TOUS les DossierPatient de la BD
- Filtre ceux qui ont un Utilisateur associé
- Envoie au template pour affichage
- Pas de pagination pour simplicité
```

**`modifier(DossierPatient $dossier, Request $request, EntityManagerInterface $em)` - Action modification**
```php
- Reçoit l'ID du dossier depuis l'URL
- Symfony charge automatiquement le DossierPatient (paramètre type-hinted)
- GET : Affiche le formulaire avec les infos actuelles
- POST : Récupère le nouvel état_greffe depuis le formulaire
  - Valide que l'état n'est pas vide
  - Met à jour l'entité
  - Sauvegarde en BD avec flush()
  - Affiche un message de succès
  - Redirige vers la liste
```

---

#### 2️⃣ Templates

**`templates/medecin/dossiers_greffe_liste.html.twig`**
- Affiche un tableau avec :
  - Nom du patient
  - Prénom
  - Date de naissance
  - État de la greffe actuel (en badge)
  - Bouton "Modifier" pour chaque patient
- Messages flash pour succès/erreur
- Bouton retour vers le tableau de bord
- Héritage du layout médecin

**`templates/medecin/dossiers_greffe_modifier.html.twig`**
- Affiche le nom et prénom du patient (read-only)
- Formulaire avec SELECT pour l'état de la greffe
- Options disponibles :
  - En attente
  - Prévu
  - Réalisé
  - Annulé
  - Reporté
- Boutons "Enregistrer" et "Annuler"
- Héritage du layout médecin

---

#### 3️⃣ Mise à Jour

**`templates/home/medecin/accueil.html.twig`**
- Lien du bouton "Dossiers Patients" changé de `href="#"` à `href="{{ path('medecin_dossiers_greffe_liste') }}"`
- Le bouton pointe maintenant vers la liste des dossiers

---

## 🔄 Flux Utilisateur

```
1. Médecin clique sur "Dossiers Patients" 
   ↓
2. Route medecin_dossiers_greffe_liste → méthode liste()
   ↓
3. Template affiche tableau de tous les dossiers
   ↓
4. Médecin clique "Modifier" sur un dossier
   ↓
5. Route medecin_dossiers_greffe_modifier (GET) → formulaire
   ↓
6. Médecin change l'état → soumet le formulaire
   ↓
7. Route medecin_dossiers_greffe_modifier (POST) → sauvegarde
   ↓
8. Message de succès et redirection vers la liste
```

---

## 💾 Modèle de Données

### Entity `DossierPatient`
```php
- id_dossier_patient : string (clé primaire)
- Date_naissance : DateTime (nullable)
- Etat_greffe : string (nullable) ← CHAMP MODIFIÉ
- utilisateur : Utilisateur (OneToOne)
```

### Entity `Utilisateur`
```php
- id_utilisateur : int
- nom, prenom : string
- dossierPatient : DossierPatient (OneToOne inverse)
```

---

## 🔐 Sécurité

- Annotation `#[IsGranted('ROLE_MEDECIN')]` : Seuls les médecins accèdent
- Vérification `if ($dossier->getUtilisateur() === null)` : Sécurité métier
- Pas de suppression : Seulement modification

---

## ✅ Points Clés

1. **Récupération Automatique** : Symfony type-hinte `DossierPatient $dossier` et charge depuis l'ID
2. **Messages Flash** : `$this->addFlash()` pour feedback utilisateur
3. **Redirects POST** : Après POST, toujours redirection (pattern PRG = Post-Redirect-Get)
4. **Layout Hérité** : `@medecin/layout.html.twig` pour cohérence UI
5. **Validation Simple** : Vérification que l'état n'est pas vide

---

## 🚀 Tests

### Test 1 : Affichage Liste
1. Aller à `/medecin/dossiers-greffe/`
2. Vérifier que tous les patients avec dossier s'affichent

### Test 2 : Modification
1. Cliquer "Modifier" sur un patient
2. Changer l'état et soumettre
3. Vérifier le message de succès
4. Vérifier la modification en BD

---

## 📝 Notes Importantes

- **Pas de création/suppression** : Seulement consultation et modification de l'état
- **État de greffe** : Peut être NULL initialement, doit être défini avant sauvegarde
- **Date de naissance** : Affichée en read-only, non modifiable
- **Relation** : Chaque DossierPatient est lié à UN Utilisateur (OneToOne)

---

**Statut :** ✅ Implémentation Complète
