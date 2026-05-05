# Diagramme de cas d'utilisation

Ce diagramme représente les principaux acteurs et cas d'utilisation de l'application de gestion hospitalière.

```mermaid
flowchart LR
    Admin[Administrateur]
    Medecin[Médecin]
    Patient[Patient]

    subgraph Systeme[Application web de suivi hospitalier]
        UC1((S'authentifier))
        UC2((Gérer les comptes de connexion))
        UC3((Gérer les profils))
        UC4((Gérer les utilisateurs))
        UC5((Gérer les patients))
        UC6((Créer un dossier patient))
        UC7((Consulter un dossier patient))
        UC8((Modifier l'état de la greffe))
        UC9((Ajouter une greffe))
        UC10((Consulter ses informations personnelles))
        UC11((Consulter son dossier médical))
        UC12((Consulter les notes médicales))
        UC13((Consulter l'historique des greffes))
    end

    Admin --- UC1
    Admin --- UC2
    Admin --- UC3
    Admin --- UC4
    Admin --- UC5

    Medecin --- UC1
    Medecin --- UC5
    Medecin --- UC6
    Medecin --- UC7
    Medecin --- UC8
    Medecin --- UC9

    Patient --- UC1
    Patient --- UC10
    Patient --- UC11
    Patient --- UC12
    Patient --- UC13
```

## Version courte à expliquer à l'oral

L'application comporte trois acteurs principaux : l'administrateur, le médecin et le patient. L'administrateur gère les comptes, les profils et les utilisateurs. Le médecin gère les patients, crée les dossiers et met à jour les informations de greffe. Le patient accède à son espace personnel pour consulter son dossier médical, les notes et l'historique des greffes.