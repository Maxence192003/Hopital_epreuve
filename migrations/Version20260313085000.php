<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260313085000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create database schema for hospital application';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE login(
           id_login INT AUTO_INCREMENT,
           mail VARCHAR(50) NOT NULL,
           password VARCHAR(255) NOT NULL,
           PRIMARY KEY(id_login)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE greffe(
           id_greffe VARCHAR(50),
           date_greffe DATETIME,
           note_greffe TEXT,
           note_donneur TEXT,
           PRIMARY KEY(id_greffe)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE note_medical(
           id_note VARCHAR(50),
           text_note_medical VARCHAR(50),
           PRIMARY KEY(id_note)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE dossier_patient(
           id_dossier_patient VARCHAR(50),
           date_naissance DATE,
           etat_greffe VARCHAR(50),
           id_note VARCHAR(50) NOT NULL,
           id_greffe VARCHAR(50),
           PRIMARY KEY(id_dossier_patient),
           FOREIGN KEY(id_note) REFERENCES note_medical(id_note),
           FOREIGN KEY(id_greffe) REFERENCES greffe(id_greffe)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE utilisateur(
           id_utilisateur INT AUTO_INCREMENT,
           nom VARCHAR(50),
           prenom VARCHAR(50),
           ville_res VARCHAR(50),
           cp VARCHAR(50),
           id_dossier_patient VARCHAR(50),
           id_login INT NOT NULL,
           PRIMARY KEY(id_utilisateur),
           FOREIGN KEY(id_dossier_patient) REFERENCES dossier_patient(id_dossier_patient),
           FOREIGN KEY(id_login) REFERENCES login(id_login)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE profil(
           id_profil INT AUTO_INCREMENT,
           role VARCHAR(50) NOT NULL,
           id_utilisateur INT NOT NULL,
           PRIMARY KEY(id_profil),
           FOREIGN KEY(id_utilisateur) REFERENCES utilisateur(id_utilisateur)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS profil');
        $this->addSql('DROP TABLE IF EXISTS utilisateur');
        $this->addSql('DROP TABLE IF EXISTS dossier_patient');
        $this->addSql('DROP TABLE IF EXISTS note_medical');
        $this->addSql('DROP TABLE IF EXISTS greffe');
        $this->addSql('DROP TABLE IF EXISTS login');
    }
}
