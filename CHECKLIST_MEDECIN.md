# ✅ CHECKLIST - Implémentation Connexion Médecin

**Date de début :** _______________  
**Date de fin :** _______________  
**Durée totale estimée :** 30-45 min

---

## 📋 Phase 1 : Préparation (5 min)

- [ ] ✅ Lire et comprendre: `QUICK_START_MEDECIN.md`
- [ ] ✅ Lire le guide complet: `IMPLEMENTATION_MEDECIN.md`
- [ ] ✅ Regarder les diagrammes visuels
- [ ] ✅ Comprendre le flux de connexion

**Checklist 1 validée :** ____/____/____

---

## 🔧 Phase 2 : Modification de Login.php (5-10 min)

**Fichier :** `src/Entity/Login.php`

- [ ] ✅ Ouvrir le fichier `src/Entity/Login.php`
- [ ] ✅ Aller à la fin de la classe (avant la dernière `}`)
- [ ] ✅ Copier-coller la méthode `getRoles()`
- [ ] ✅ Copier-coller la méthode `eraseCredentials()`
- [ ] ✅ Vérifier la syntaxe (pas d'erreur PHP)
- [ ] ✅ Sauvegarder le fichier

**Checklist 2 validée :** ____/____/____

---

## ✨ Phase 3 : Créer MedecinController.php (10-15 min)

**Fichier à créer :** `src/Controller/MedecinController.php`

- [ ] ✅ Ouvrir l'explorateur de fichiers
- [ ] ✅ Naviguer vers `src/Controller/`
- [ ] ✅ Créer un nouveau fichier : `MedecinController.php`
- [ ] ✅ Copier-coller le code complet du guide
- [ ] ✅ Vérifier le namespace: `namespace App\Controller;`
- [ ] ✅ Vérifier l'import: `use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;`
- [ ] ✅ Sauvegarder le fichier

**Checklist 3 validée :** ____/____/____

---

## 🎨 Phase 4 : Créer le Template Médecin (15-20 min)

**Fichier à créer :** `templates/medecin/accueil.html.twig`

- [ ] ✅ Naviguer vers `templates/`
- [ ] ✅ Créer un nouveau dossier : `medecin`
- [ ] ✅ Créer un nouveau fichier : `accueil.html.twig` dans ce dossier
- [ ] ✅ Copier-coller le code complet du guide
- [ ] ✅ Vérifier l'héritage Twig: `{% extends "base.html.twig" %}`
- [ ] ✅ Vérifier les variables: `{{ medecin.prenom }}`, `{{ user.mail }}`
- [ ] ✅ Sauvegarder le fichier

**Checklist 4 validée :** ____/____/____

---

## 🔐 Phase 5 : Modifier LoginSuccessHandler.php (5 min)

**Fichier :** `src/Security/LoginSuccessHandler.php`

- [ ] ✅ Ouvrir le fichier `src/Security/LoginSuccessHandler.php`
- [ ] ✅ Trouver la méthode `onAuthenticationSuccess()`
- [ ] ✅ Remplacer le corps de la méthode par le nouveau code
- [ ] ✅ Vérifier le nouvel ordre de priorité:
  - [ ] ROLE_ADMIN → /admin
  - [ ] ROLE_MEDECIN → /medecin ✨
  - [ ] Défaut → /home
- [ ] ✅ Sauvegarder le fichier

**Checklist 5 validée :** ____/____/____

---

## 🗄️ Phase 6 : Créer un Utilisateur Médecin en BDD (5-10 min)

**Méthode au choix :**

### Option A : Via EasyAdmin (le plus simple)

- [ ] ✅ Ouvrir http://localhost:8000/admin
- [ ] ✅ Se connecter avec vos identifiants admin
- [ ] ✅ Aller dans l'onglet **Login CRUD**
  - [ ] Créer un nouveau Login
  - [ ] Email: `medecin@hopital.fr`
  - [ ] Mot de passe: `password123`
  - [ ] Sauvegarder et noter l'ID (ex: 5)
- [ ] ✅ Aller dans l'onglet **Utilisateur CRUD**
  - [ ] Créer un nouvel Utilisateur
  - [ ] Nom: `Dupont`
  - [ ] Prénom: `Jean`
  - [ ] Ville: `Paris`
  - [ ] Code Postal: `75000`
  - [ ] Login: Sélectionner le Login créé ci-dessus
  - [ ] Sauvegarder et noter l'ID (ex: 10)
- [ ] ✅ Aller dans l'onglet **Profil CRUD**
  - [ ] Créer un nouveau Profil
  - [ ] Rôle: `medecin` (⚠️ ATTENTION: case-sensitive = minuscule!)
  - [ ] Utilisateur: Sélectionner l'Utilisateur créé ci-dessus
  - [ ] Sauvegarder

**Checklist 6A validée :** ____/____/____

### Option B : Via SQL (avancé)

- [ ] ✅ Ouvrir votre client MySQL/PhpMyAdmin
- [ ] ✅ Sélectionner votre base de données
- [ ] ✅ Exécuter ces 3 requêtes (remplacer les IDs):

```sql
-- 1. Login
INSERT INTO login (mail, password) 
VALUES ('medecin@hopital.fr', '$2y$10$...');

-- 2. Utilisateur (remplacer id_login)
INSERT INTO utilisateur (nom, prenom, ville_res, cp, id_login) 
VALUES ('Dupont', 'Jean', 'Paris', '75000', 5);

-- 3. Profil (remplacer id_utilisateur)
INSERT INTO profil (role, id_utilisateur) 
VALUES ('medecin', 10);
```

- [ ] ✅ Vérifier les insertions avec SELECT
- [ ] ✅ S'assurer que le `role` est en minuscule: `medecin`

**Checklist 6B validée :** ____/____/____

### Option C : Via Commande PHP (pour plus tard)

- [ ] ⏭️ Créer `src/Command/CreateMedecinCommand.php` (optionnel pour Phase 2)
- [ ] ⏭️ Exécuter: `php bin/console app:create-medecin`

**Checklist 6C validée:** ____/____/____ (optionnel)

---

## 🧪 Phase 7 : Tester la Connexion (5 min)

### Test 1 : Accéder à la page de login

- [ ] ✅ Ouvrir http://localhost:8000/login
- [ ] ✅ La page de connexion s'affiche correctement

### Test 2 : Essayer avec mauvais identifiants

- [ ] ✅ Email: `medecin@hopital.fr`
- [ ] ✅ Mot de passe: `wrongpassword`
- [ ] ✅ Cliquer "Se connecter"
- [ ] ✅ Vérifier : message d'erreur affiché ❌

### Test 3 : Connexion avec bons identifiants

- [ ] ✅ Email: `medecin@hopital.fr`
- [ ] ✅ Mot de passe: `password123`
- [ ] ✅ Cliquer "Se connecter"
- [ ] ✅ **RÉSULTAT ATTENDU :** Redirection vers http://localhost:8000/medecin

### Test 4 : Vérifier la page médecin

- [ ] ✅ L'URL actuelle est: `http://localhost:8000/medecin`
- [ ] ✅ Le titre affiche: "Bienvenue Dr. Jean DUPONT"
- [ ] ✅ Le prénom affiché: "Jean"
- [ ] ✅ Le nom affiché: "DUPONT"
- [ ] ✅ L'email affiché: "medecin@hopital.fr"
- [ ] ✅ La badge rôle affiche: "MÉDECIN"
- [ ] ✅ Les boutons affichent correctement
- [ ] ✅ Le bouton "Se déconnecter" est présent

### Test 5 : Test déconnexion

- [ ] ✅ Cliquer sur "Se déconnecter"
- [ ] ✅ Être redirigé vers la page d'accueil
- [ ] ✅ Essayer d'accéder à `/medecin` directement
- [ ] ✅ Être redirigé vers la page de login (accès refusé)

**Checklist 7 validée :** ____/____/____

---

## 🔍 Phase 8 : Vérifier les Erreurs (5 min)

Si vous avez des problèmes, cochez ces vérifications:

### Erreur: "Route 'app_medecin_accueil' not found"
- [ ] ✅ Redémarrer le serveur Symfony: `symfony server:stop` puis `symfony server:start`
- [ ] ✅ Vérifier que `MedecinController.php` existe dans `src/Controller/`
- [ ] ✅ Vérifier le namespace: `App\Controller`
- [ ] ✅ Vérifier la route: `#[Route('', name: 'app_medecin_accueil')]`

### Erreur: "The method getRoles does not exist"
- [ ] ✅ Vérifier que `getRoles()` est dans `src/Entity/Login.php`
- [ ] ✅ Vérifier la syntaxe: `public function getRoles(): array`
- [ ] ✅ Vérifier qu'il n'y a pas de typos

### Page blanche ou erreur 500
- [ ] ✅ Vérifier les logs: `var/log/dev.log`
- [ ] ✅ Cocher la syntaxe Twig dans `templates/medecin/accueil.html.twig`
- [ ] ✅ Vérifier que les variables `{{ medecin }}` et `{{ user }}` sont passées

### Redirection vers `/home` au lieu de `/medecin`
- [ ] ✅ Vérifier que l'utilisateur a bien un Profil avec `role = 'medecin'`
- [ ] ✅ ⚠️ Vérifier la casse: doit être minuscules `medecin` PAS `Medecin` ou `MEDECIN`
- [ ] ✅ Vérifier dans `LoginSuccessHandler.php` qu'il y a la ligne:
  ```php
  if (in_array('ROLE_MEDECIN', $roles)) {
  ```

### Erreur "Access Denied"
- [ ] ✅ Vérifier que l'utilisateur a bien un Profil
- [ ] ✅ Vérifier que le rôle du Profil est `medecin`
- [ ] ✅ Vérifier que le contrôleur a `#[IsGranted('ROLE_MEDECIN')]`

**Checklist 8 validée :** ____/____/____

---

## 📊 Phase 9 : Validation Finale (2 min)

- [ ] ✅ Tous les fichiers ont été modifiés/créés
- [ ] ✅ Aucune erreur visible
- [ ] ✅ La connexion médecin fonctionne
- [ ] ✅ La page médecin s'affiche correctement
- [ ] ✅ Les données affichées sont correctes
- [ ] ✅ La déconnexion fonctionne
- [ ] ✅ L'accès non-autorisé est bloqué (sans profil)

**VALIDATION FINALE :** ✅ Complétée le ____/____/____

---

## 🎉 Résumé d'Implémentation

| Élément | Créé | Modifié | Testé |
|---------|------|---------|-------|
| `Login.php` | | ✅ | ✅ |
| `LoginSuccessHandler.php` | | ✅ | ✅ |
| `MedecinController.php` | ✅ | | ✅ |
| `accueil.html.twig` | ✅ | | ✅ |
| Utilisateur Médecin (BDD) | ✅ | | ✅ |

---

## 📞 Notes Personnelles

Notez ici tous les problèmes rencontrés ou solutions trouvées:

```
_________________________________________________________________

_________________________________________________________________

_________________________________________________________________

_________________________________________________________________

_________________________________________________________________
```

---

## ✨ Prochaines Étapes (Optionnel)

Une fois que tout fonctionne, vous pouvez:

- [ ] Créer d'autres pages médecin (`patients.html.twig`, `dossiers.html.twig`)
- [ ] Ajouter des fonctionnalités (voir patients, modifier notes)
- [ ] Améliorer le design de la page
- [ ] Ajouter une barre de navigation
- [ ] Créer des rapports/statistiques
- [ ] Documenter l'API

---

**FIN DE LA CHECKLIST ! 🎉**

