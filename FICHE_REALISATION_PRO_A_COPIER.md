# Fiche E6 - Réalisation professionnelle

## Informations générales

- N° réalisation : 1
- Nom, prénom : Brullon Maxence
- N° candidat : à compléter
- Modalité d'évaluation : Contrôle en cours de formation
- Date : 01-02 / 06 / 2026

## Organisation support de la réalisation professionnelle

Projet réalisé dans le cadre de la formation BTS SIO, à partir d'un cahier des charges fourni par l'équipe pédagogique.

## Intitulé de la réalisation professionnelle

Application web de suivi des dossiers patients et des greffes hépatiques sous Symfony

## Période, lieu et modalité

- Période de réalisation : 03/2026 à 04/2026
- Lieu : Limoges - Campus Beaupeyrat
- Modalité : Seul(e)

## Compétences travaillées

- Concevoir et développer une solution applicative
- Assurer la maintenance corrective ou évolutive d'une solution applicative
- Gérer les données

## Conditions de réalisation

Le projet a été réalisé à partir d'un cahier des charges pédagogique. L'objectif était de concevoir une application web métier répondant à un besoin concret de suivi hospitalier.

La solution permet aux administrateurs de gérer les comptes et profils, aux médecins de créer et suivre les dossiers patients et les greffes, et aux patients de consulter leur dossier dans un espace sécurisé.

## Description des ressources documentaires, matérielles et logicielles utilisées

- Cahier des charges et documentation projet
- Docker et Docker Compose
- Visual Studio Code
- Symfony 8
- Doctrine ORM
- Twig
- MySQL et phpMyAdmin
- Looping pour la modélisation de la base de données
- Git et GitHub pour le versionnement
- EasyAdmin pour l'interface d'administration
- PHPUnit pour les vérifications

## Modalités d'accès aux productions et à leur documentation

- Dépôt GitHub : https://github.com/Maxence192003/Hopital_epreuve.git
- Documentation technique : README.md et dossier aides/
- Accès local au projet via l'environnement Docker de développement

## Descriptif de la réalisation professionnelle

Le projet consiste à concevoir et développer une application web de gestion hospitalière orientée suivi des patients et des greffes hépatiques au sein d'un institut spécialisé.

L'objectif est de proposer une plateforme sécurisée adaptée à trois profils utilisateurs : administrateur, médecin et patient.

L'administrateur dispose d'une interface EasyAdmin pour gérer les comptes, les profils, les patients, les dossiers et les greffes.

Le médecin peut créer un dossier patient, consulter son contenu, mettre à jour l'état de la greffe et enregistrer les informations de transplantation et de suivi.

Le patient peut accéder à son espace personnel afin de consulter son dossier médical, ses notes et l'historique de ses greffes.

L'application a été développée avec Symfony 8 selon une architecture MVC, en s'appuyant sur Doctrine ORM, Twig, Stimulus et une base de données MySQL conteneurisée avec Docker.

## Productions réalisées / fonctionnalités principales

- Authentification sécurisée avec redirection selon le rôle : administrateur, médecin ou patient
- Gestion des patients et des dossiers médicaux : création, consultation, modification
- Gestion des greffes : date de greffe, note de greffe, note donneur, état de la greffe
- Interface d'administration avec EasyAdmin
- Espace patient sécurisé pour consulter le dossier médical
- Architecture MVC sous Symfony 8 avec persistance des données via Doctrine ORM
- Interface web responsive et exécution du projet dans un environnement Docker
- Documentation technique associée dans le README et le dossier aides/

## Schémas explicatifs

Schéma de la base de données réalisé sous Looping puis exploité dans phpMyAdmin.

La base de données repose sur 6 tables principales :

- Login : id_login, mail, password
- Profil : id_profil, role
- Utilisateur : id_utilisateur, nom, prenom, ville_res, cp, id_login, id_profil
- DossierPatient : id_dossier_patient, date_naissance, etat_greffe, id_utilisateur
- NoteMedical : id_note, text_note_medical, created_at, id_dossier_patient
- Greffe : id_greffe, date_greffe, note_greffe, note_donneur, id_dossier_patient

Relations principales du schéma :

- Un login peut être associé à plusieurs utilisateurs
- Un profil peut être associé à plusieurs utilisateurs
- Un utilisateur possède un seul profil
- Un utilisateur peut être lié à un dossier patient
- Un dossier patient peut contenir plusieurs notes médicales
- Un dossier patient peut contenir plusieurs greffes

Correction importante pour le schéma :

La table Profil ne contient pas de champ id_utilisateur. La relation correcte est portée par la table Utilisateur via la clé étrangère id_profil. De la même manière, la liaison entre Utilisateur et DossierPatient se fait par id_utilisateur dans DossierPatient.

## Annexes à mentionner dans la fiche

- Capture d'écran de l'interface médecin
- Capture d'écran d'un dossier patient
- Capture d'écran de l'interface patient
- Capture d'écran de l'administration EasyAdmin
- Schéma relationnel corrigé de la base de données avec les tables Login, Profil, Utilisateur, DossierPatient, NoteMedical et Greffe
- Lien vers le dépôt GitHub et la documentation du projet

## Version plus courte si tu manques de place

Application web Symfony 8 permettant la gestion sécurisée des patients, des dossiers médicaux et des greffes hépatiques. Le projet repose sur une architecture MVC avec Doctrine ORM, Twig, MySQL, Docker et EasyAdmin. Trois profils sont gérés : administrateur, médecin et patient. Les fonctionnalités principales couvrent l'authentification, la gestion des comptes et profils, la création et la consultation des dossiers patients, le suivi des greffes et l'accès sécurisé du patient à son dossier.