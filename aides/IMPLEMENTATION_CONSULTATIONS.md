# 📋 Consultations - Documentation Complète

## 🎯 Fonctionnalité
Module permettant au médecin de :
- ✅ Consulter la liste de tous ses patients (ayant un dossier)
- ✅ Rechercher par email, prénom ou nom
- ✅ Voir les informations détaillées d'un patient
- ✅ Ajouter et consulter les notes médicales (plusieurs notes possibles)
- ✅ Voir les dates de création des notes

---

## 📂 Fichiers Modifiés/Créés

| Fichier | Type | Description |
|---------|------|-------------|
| `src/Controller/MedecinController.php` | Modifié | 3 actions + fix timezone (Europe/Paris) |
| `src/Entity/NoteMedical.php` | Modifié | Ajout champ `created_at` (DateTime) |
| `src/Repository/DossierPatientRepository.php` | Modifié | Méthode `findBySearchConsultation()` pour la recherche |
| `migrations/Version20260320133000.php` | Créé | Migration : ajoute colonne `created_at` |
| `templates/home/medecin/consultations/liste.html.twig` | Créé | Liste + recherche des patients |
| `templates/home/medecin/consultations/detail.html.twig` | Créé | Détails patient + notes accordéon + formulaire |
| `templates/home/medecin/accueil.html.twig` | Modifié | Bouton "Consultations" activé |

---

## 🔄 Flux Utilisateur

```
1️⃣  Médecin sur le tableau de bord
         ↓
2️⃣  Clique sur "Consultations"
         ↓
3️⃣  Page liste des patients
    - Voir tous les patients avec dossier
    - Barre de recherche (email, prénom, nom)
         ↓
4️⃣  Clique sur un patient
         ↓
5️⃣  Page détails
    - Info complète du patient
    - Toutes ses notes (triées du plus récent)
    - Formulaire pour ajouter une note
         ↓
6️⃣  Soumet note → Enregistrement + retour page
```

---

## 🛠️ Routes Disponibles

| Route | Nom | Méthode | Description |
|-------|-----|---------|-------------|
| `/medecin/consultations` | `medecin_consultations` | GET | Liste des patients + recherche |
| `/medecin/consultations/{id}` | `medecin_consultation_detail` | GET | Détails patient + notes |
| `/medecin/consultations/{id}/note` | `medecin_consultation_add_note` | POST | Ajouter une note médicale |

---

## 📝 Actions du Controller

### 1. `consultations()` – Liste des patients
```
Entrée : Paramètre GET "search" (optionnel)
Sortie : Liste filtrée ou complète des dossiers
Affiche : liste.html.twig
```

### 2. `consultationDetail($id)` – Détails du patient
```
Entrée : ID du dossier (ParamConverter)
Sortie : Dossier + ses notes médicales
Affiche : detail.html.twig
```

### 3. `addNoteMedical($id)` – Ajouter une note
```
Entrée : ID dossier + texte note (POST)
Traitement : 
  - Créer NoteMedical
  - Générer ID unique (uniqid)
  - Enregistrer date création
  - Sauvegarder en BD
Sortie : Redirection vers détail patient
```

---

## 🔍 Recherche - Comment ça marche

**Méthode :** `findBySearchConsultation($search)`

Cherche dans 3 champs :
- `Utilisateur.nom` (LIKE)
- `Utilisateur.prenom` (LIKE)
- `Login.mail` (LIKE)

Exemple : Rechercher "jean" retourne :
- Tous les patients avec nom contenant "jean"
- Tous les patients avec prénom contenant "jean"
- Tous les patients avec email contenant "jean"

---

## 📅 Notes Médicales

### Champs
| Champ | Type | Description |
|-------|------|-------------|
| `id_note` | string(50) | ID unique générée avec `uniqid()` |
| `text_note_medical` | string(50) | Contenu de la note |
| `created_at` | DateTime | Date/heure de création (Europe/Paris) |
| `id_dossier_patient` | FK | Lien vers le dossier patient |

### Affichage & Interaction
- **Tri :** Du plus récent au plus ancien
- **Format date :** "20 mars 2026 à 14:30" (français)
- **Système Accordéon :**
  - ▶ **Par défaut** : Notes repliées (juste la date visible)
  - 👆 **Au clic** : Note se déplie et montre le contenu
  - 👆 **Reclic** : Note se replie
  - Arrow icon tourne (▶ → ▼) au déploiement

### Fuseau Horaire
- ✅ Les notes sont enregistrées avec timezone **Europe/Paris**
- ✅ Les heures affichées correspondent à l'heure locale (pas UTC)
- Exemple : Note à 16:30 → Affichée à 16:30

---

## 🚀 Installation/Déploiement

### 1. Installer les dépendances Twig
```bash
docker-compose exec -T php composer require twig/intl-extra
```
✅ Active le filtre `format_datetime` pour les dates internationalisées

### 2. Appliquer la migration BD
```bash
docker-compose exec -T php php bin/console doctrine:migrations:migrate --no-interaction
```
✅ Ajoute la colonne `created_at` à la table `note_medical`

### 3. Vérifier les fichiers
- [x] Controller modifié (avec timezone)
- [x] Entity modifié
- [x] Repository modifié
- [x] 2 templates créés
- [x] Migration appliquée
- [x] Dépendances Twig installées

### 4. Tester
- [ ] Accéder à `/medecin/consultations`
- [ ] Voir la liste des patients
- [ ] Rechercher un patient
- [ ] Cliquer sur un patient
- [ ] Cliquer sur une note → déplie
- [ ] Cliquer à nouveau → replie
- [ ] Ajouter une nouvelle note
- [ ] Vérifier l'heure affichée correspond à l'heure saisie

---

## ⚠️ Contraintes/Limitations

| Contrainte | Note |
|-----------|------|
| Seuls les patients avec `DossierPatient` | Les patients pure n'apparaissent pas |
| Recherche sensible à la casse | "Jean" ≠ "jean" (selon DB config) |
| Champ texte note limité à 50 caractères | ⚠️ À augmenter si nécessaire |
| Une seule note max par requête | L'ajout se fait en POST simple |

---

## 🔧 Personnalisation Possible

### Augmenter la taille du texte de note
Fichier : `src/Entity/NoteMedical.php`
```php
// Avant (50 caractères)
#[ORM\Column(length: 50, nullable: true)]

// Après (TEXT - illimité)
#[ORM\Column(type: Types::TEXT, nullable: true)]
```
Puis créer une migration

### Modifier le fuseau horaire
Fichier : `src/Controller/MedecinController.php`
```php
// Ligne dans addNoteMedical()
$now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

// Changer 'Europe/Paris' par :
// 'Europe/London'
// 'America/New_York'
// 'UTC'
// etc.
```

### Désactiver le système accordéon
Fichier : `templates/home/medecin/consultations/detail.html.twig`
```twig
<!-- Remplacer le boutton toggle par un affichage simple -->
<!-- Ou supprimer la fonction JavaScript toggleNote() -->
```

### Ajouter un champ "médecin auteur"
Ajouter une FK vers `Utilisateur` (le médecin qui a écrit la note)

### Ajouter modification/suppression de note
Créer des actions `updateNoteMedical()` et `deleteNoteMedical()` + templates modaux

---

## 🧪 Données de Test

### Créer un patient avec dossier
```sql
-- 1. Créer login
INSERT INTO login (mail, password) VALUES ('jean@hopital.fr', 'hash...');

-- 2. Créer utilisateur
INSERT INTO utilisateur (nom, prenom, ville_res, cp, id_login, id_profil)
VALUES ('Dupont', 'Jean', 'Limoges', '87000', 1, 3);

-- 3. Créer dossier patient
INSERT INTO dossier_patient (id_dossier_patient, etat_greffe, id_utilisateur)
VALUES ('DP001', 'En attente', 1);
```

### Tester la recherche
- Rechercher "dupont" → Doit trouver Jean Dupont
- Rechercher "jean@hopital" → Doit trouver Jean Dupont
- Rechercher "jean" → Doit trouver Jean Dupont

---

## 📊 Schéma des Relations

```
LOGIN (id_login, mail, password)
   │
   ├─→ UTILISATEUR (id_utilisateur, nom, prenom, ville_res, cp)
   │      │
   │      └─→ DOSSIER_PATIENT (id_dossier, etat_greffe)
   │             │
   │             └─→ NOTE_MEDICAL (id_note, text, created_at) ✅ PLUSIEURS NOTES
   │
   └─→ PROFIL (id_profil, role)
```

---

## ✅ Checklist Finale

- [x] Controller implémenté (3 actions + timezone)
- [x] Entity modifiée (ajout created_at)
- [x] Repository modifié (recherche)
- [x] Migration créée et appliquée
- [x] Templates créés (liste + détail avec accordéon)
- [x] Bouton accueil activé
- [x] Dépendance twig/intl-extra installée
- [x] Dates natives (format français)
- [x] Notes triées récent → ancien
- [x] Notes cachées par défaut (accordéon)
- [x] Fuseau horaire Europe/Paris configuré
- [x] Icône ▶/▼ au déploiement

**Status : ✅ COMPLET, AMÉLIORÉ ET OPTIMISÉ**

---

## 📋 Résumé des Améliorations

### ✨ Depuis la création initiale :

1. **Système d'accordéon** 
   - Notes repliées par défaut (moins de défilement)
   - Clique pour voir/cacher les détails
   - Arrow icon animée (▶ → ▼)

2. **Fuseau horaire corrigé**
   - Les notes s'enregistrent en Europe/Paris
   - Plus de décalage d'1 heure
   - Heure affichée = heure réelle

3. **Formatage des dates**
   - Extension twig/intl-extra installée
   - Dates en français ("20 mars 2026 à 14:30")
   - Format lisible et professionnel

4. **UX Améliorée**
   - Interface plus compacte
   - Moins de charge visuelle
   - Navigation plus fluide

    <main class="medecin-main">
        <div class="medecin-dashboard">
            
            <!-- Fiche Patient -->
            <section class="patient-details">
                <h2>📌 Informations Patient</h2>
                <div class="details-grid">
                    <div class="detail-item">
                        <strong>Prénom :</strong>
                        <span>{{ dossier.utilisateur.prenom }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Nom :</strong>
                        <span>{{ dossier.utilisateur.nom|upper }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Email :</strong>
                        <span>{{ dossier.utilisateur.login.mail }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Ville :</strong>
                        <span>{{ dossier.utilisateur.villeRes }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Code Postal :</strong>
                        <span>{{ dossier.utilisateur.cp }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>État Greffe :</strong>
                        <span>{{ dossier.etatGreffe ?? 'Non défini' }}</span>
                    </div>
                </div>
            </section>

            <!-- Notes Médicales Existantes -->
            <section class="notes-section">
                <h2>📝 Notes Médicales ({{ notes|length }})</h2>
                
                {% if notes|length > 0 %}
                    <div class="notes-list">
                        {% for note in notes %}
                            <div class="note-card">
                                <div class="note-content">
                                    {{ note.textNoteMedical }}
                                </div>
                                <div class="note-id">
                                    ID: {{ note.idNote }}
                                </div>
                            </div>
                        {% endfor %}
                    </div>
                {% else %}
                    <p class="no-notes">Aucune note médicale pour ce patient</p>
                {% endif %}
            </section>

            <!-- Formulaire Ajouter Note -->
            <section class="add-note-section">
                <h2>✍️ Ajouter une Note Médicale</h2>
                <form method="POST" action="{{ path('medecin_consultation_add_note', {id: dossier.idDossierPatient}) }}" class="note-form">
                    <div class="form-group">
                        <label for="texte_note">Contenu de la note :</label>
                        <textarea 
                            id="texte_note"
                            name="texte_note"
                            class="form-textarea"
                            placeholder="Entrez votre note médicale..."
                            required
                        ></textarea>
                    </div>
                    <button type="submit" class="btn-submit">💾 Enregistrer la note</button>
                </form>
            </section>

            <footer class="medecin-footer">
                <p>&copy; 2026 Institut de Greffe de Foie Limoges. Espace médecin réservé.</p>
            </footer>
        </div>
    </main>
</div>

<style>
.patient-details {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    border-left: 4px solid #007bff;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    background: #f8f9fa;
    padding: 12px;
    border-radius: 5px;
}

.detail-item strong {
    color: #333;
    font-size: 13px;
    margin-bottom: 5px;
}

.detail-item span {
    color: #666;
    font-size: 14px;
}

.notes-section {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    border-left: 4px solid #28a745;
}

.notes-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 15px;
}

.note-card {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid #28a745;
}

.note-content {
    color: #333;
    line-height: 1.6;
    margin-bottom: 8px;
    word-wrap: break-word;
}

.note-id {
    font-size: 12px;
    color: #999;
}

.no-notes {
    color: #999;
    font-style: italic;
    padding: 15px;
}

.add-note-section {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    border-left: 4px solid #ffc107;
}

.note-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-top: 15px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 8px;
    font-weight: bold;
    color: #333;
}

.form-textarea {
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-family: Arial, sans-serif;
    font-size: 14px;
    min-height: 150px;
    resize: vertical;
}

.form-textarea:focus {
    outline: none;
    border-color: #ffc107;
    box-shadow: 0 0 5px rgba(255, 193, 7, 0.3);
}

.btn-submit {
    padding: 12px 20px;
    background: #ffc107;
    color: #333;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    font-size: 14px;
    transition: background 0.3s;
}

.btn-submit:hover {
    background: #e0a800;
}
</style>
{% endblock %}
```

---

### **Étape 5 : Activer le Bouton dans l'Accueil**

**Fichier : `templates/home/medecin/accueil.html.twig`**

Remplacer le bouton "Consultations" désactivé par un lien actif :

```twig
<!-- Consultations -->
<div class="medecin-card">
    <div class="medecin-card-icon">👨‍⚕️</div>
    <h3>Consultations</h3>
    <p>Gérer les consultations avec vos patients</p>
    <a href="{{ path('medecin_consultations') }}" class="medecin-card-btn">Accéder</a>
</div>
```

---

## ✅ Checklist d'Implémentation

- [ ] Ajouter les 3 actions dans `MedecinController.php`
- [ ] Importer les classes nécessaires
- [ ] Créer la méthode `findBySearchConsultation()` dans `DossierPatientRepository.php`
- [ ] Créer le dossier `templates/home/medecin/consultations/`
- [ ] Créer `liste.html.twig`
- [ ] Créer `detail.html.twig`
- [ ] Modifier le bouton dans `accueil.html.twig`
- [ ] Tester la navigation

---

## 🧪 Test Fonctionnel

1. **Médecin se connecte** → Tableau de bord
2. **Clique sur "Consultations"** → Liste des patients avec dossier
3. **Recherche = test** → Filtre par email/prénom/nom
4. **Clique sur un patient** → Affiche ses infos et notes
5. **Ajoute une note** → La note s'enregistre et s'affiche

---

## 🐛 Dépannage Courant

| Problème | Solution |
|---------|----------|
| Erreur "route not found" | Vérifier les noms des routes dans les `#[Route]` |
| Notes ne s'affichent pas | Vérifier que `getNotesMedicales()` existe dans `DossierPatient` |
| Recherche ne marche pas | Vérifier la méthode `findBySearchConsultation()` dans le Repository |
| Template introuvable | Vérifier le chemin exact du dossier `templates/home/medecin/consultations/` |

