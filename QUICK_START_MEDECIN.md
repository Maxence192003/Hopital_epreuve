# ⚡ Guide Rapide - Connexion Médecin en 7 Étapes

**Temps total : 30-45 minutes**

---

## ✅ ÉTAPE 1 : Ajouter getRoles() à Login.php (5 min)

**Fichier :** `src/Entity/Login.php`

Ajoutez cette méthode à la fin de la classe (avant la dernière accolade) :

```php
public function getRoles(): array
{
    $roles = ['ROLE_USER'];
    
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
}
```

---

## ✅ ÉTAPE 2 : Créer MedecinController.php (10 min)

**Fichier à créer :** `src/Controller/MedecinController.php`

Copier-coller:

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
        $user = $this->getUser();
        $utilisateurs = $user->getUtilisateurs();
        $medecin = $utilisateurs->first() ?? null;
        
        return $this->render('medecin/accueil.html.twig', [
            'medecin' => $medecin,
            'user' => $user,
        ]);
    }
}
```

---

## ✅ ÉTAPE 3 : Créer le template accueil.html.twig (15 min)

**Fichier à créer :** `templates/home/medecin/accueil.html.twig`

Copier-coller:

```twig
{% extends "base.html.twig" %}

{% block title %}Accueil Médecin{% endblock %}

{% block body %}
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">👨‍⚕️ Bienvenue Dr. {{ medecin.prenom }} {{ medecin.nom|upper }}</h2>
        </div>
        <div class="card-body p-4">
            <h4>📋 Informations</h4>
            <p><strong>Email :</strong> {{ user.mail }}</p>
            <p><strong>Rôle :</strong> <span class="badge bg-primary">MÉDECIN</span></p>
            <hr>
            <a href="{{ path('app_logout') }}" class="btn btn-danger">Se déconnecter</a>
        </div>
    </div>
</div>
{% endblock %}
```

---

## ✅ ÉTAPE 4 : Mettre à jour LoginSuccessHandler (5 min)

**Fichier :** `src/Security/LoginSuccessHandler.php`

Remplacez la méthode `onAuthenticationSuccess()` :

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

## ✅ ÉTAPE 5 : Créer un Médecin en BDD (5-10 min)

### Option rapide : Via EasyAdmin

1. Allez sur http://localhost:8000/admin (admin)
2. **Login** → Créer : `medecin@test.fr` / `password123`
3. **Utilisateur** → Créer : Dupont / Jean / Paris / 75000 (lié au Login)
4. **Profil** → Créer : Rôle = `medecin` (lié à l'Utilisateur)

### Option SQL (+ rapide)

Remplacez la valeur de `id_login` par le vrai ID du Login créé:

```sql
-- 1. Login
INSERT INTO login (mail, password) VALUES ('medecin@test.fr', '$2y$10$...');

-- 2. Utilisateur (remplacez 5 par le vrai id_login)
INSERT INTO utilisateur (nom, prenom, ville_res, cp, id_login) 
VALUES ('Dupont', 'Jean', 'Paris', '75000', 5);

-- 3. Profil (remplacez 10 par le vrai id_utilisateur)
INSERT INTO profil (role, id_utilisateur) VALUES ('medecin', 10);
```

---

## ✅ ÉTAPE 6 : Tester (5 min)

1. Allez sur : http://localhost:8000/login
2. Connectez-vous :
   - Email : `medecin@test.fr`
   - Mot de passe : `password123`
3. **Résultat attendu :** Redirection vers /medecin avec bienvenue

---

## ✅ ÉTAPE 7 : Vérifier tout fonctionne

Si page blanche ou erreur → Voir section "Troubleshooting"

---

## 🚨 Problèmes Courants

| Problème | Solution |
|----------|----------|
| Route `app_medecin_accueil` inexistante | Redémarrer le serveur Symfony |
| Erreur `getRoles does not exist` | Vérifier que la méthode est dans `Login.php` |
| Redirection vers `/home` au lieu de `/medecin` | Vérifier le rôle `profil.role = 'medecin'` (case!) |
| Erreur "Access denied" | Le profil doit être lié à l'utilisateur |

---

## 📚 Fichiers créés/modifiés

✅ `src/Entity/Login.php` - MODIFIÉ  
✅ `src/Security/LoginSuccessHandler.php` - MODIFIÉ  
✅ `src/Controller/MedecinController.php` - CRÉÉ  
✅ `templates/medecin/accueil.html.twig` - CRÉÉ  
✅ Base de données - 3 entrées créées (Login, Utilisateur, Profil)

**C'est tout ! Vous avez réussi ! 🎉**

