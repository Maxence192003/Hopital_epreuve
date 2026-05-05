# Diagrammes de cas d'utilisation par utilisateur

Ce document présente un diagramme de cas d'utilisation distinct pour chaque type d'utilisateur de l'application.

## 1. Administrateur

```mermaid
flowchart LR
    Admin[Administrateur]

    subgraph Systeme[Application web de suivi hospitalier]
        UC1((S'authentifier))
        UC2((Gérer les comptes de connexion))
        UC3((Gérer les profils))
        UC4((Gérer les utilisateurs))
        UC5((Gérer les patients))
        UC6((Gérer les dossiers patients))
        UC7((Gérer les greffes))
    end

    Admin --- UC1
    Admin --- UC2
    Admin --- UC3
    Admin --- UC4
    Admin --- UC5
    Admin --- UC6
    Admin --- UC7
```

Résumé : l'administrateur accède à l'interface d'administration pour gérer les comptes, les profils, les utilisateurs, les patients, les dossiers et les greffes.

## 2. Médecin

```mermaid
flowchart LR
    Medecin[Médecin]

    subgraph Systeme[Application web de suivi hospitalier]
        UC1((S'authentifier))
        UC2((Consulter les patients))
        UC3((Créer un dossier patient))
        UC4((Consulter un dossier patient))
        UC5((Modifier l'état de la greffe))
        UC6((Ajouter une greffe))
        UC7((Consulter les informations de greffe))
    end

    Medecin --- UC1
    Medecin --- UC2
    Medecin --- UC3
    Medecin --- UC4
    Medecin --- UC5
    Medecin --- UC6
    Medecin --- UC7
```

Résumé : le médecin suit les patients, crée et consulte les dossiers, puis met à jour les données liées aux greffes.

## 3. Patient

```mermaid
flowchart LR
    Patient[Patient]

    subgraph Systeme[Application web de suivi hospitalier]
        UC1((S'authentifier))
        UC2((Consulter ses informations personnelles))
        UC3((Consulter son dossier médical))
        UC4((Consulter les notes médicales))
        UC5((Consulter l'historique des greffes))
    end

    Patient --- UC1
    Patient --- UC2
    Patient --- UC3
    Patient --- UC4
    Patient --- UC5
```

Résumé : le patient se connecte à son espace sécurisé pour consulter ses informations, son dossier médical, ses notes et l'historique de ses greffes.