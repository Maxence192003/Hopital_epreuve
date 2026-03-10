<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260310073727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE login (id_login INT AUTO_INCREMENT NOT NULL, mail VARCHAR(100) NOT NULL, password VARCHAR(255) NOT NULL, id_utilisateur INT NOT NULL, UNIQUE INDEX UNIQ_AA08CB105126AC48 (mail), UNIQUE INDEX UNIQ_AA08CB1050EAE44 (id_utilisateur), PRIMARY KEY (id_login)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE profil (id_profil INT AUTO_INCREMENT NOT NULL, role VARCHAR(50) NOT NULL, PRIMARY KEY (id_profil)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE utilisateur (id_utilisateur INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, ville_res VARCHAR(100) NOT NULL, cp VARCHAR(10) NOT NULL, id_profil INT NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3C0E1077A (id_profil), PRIMARY KEY (id_utilisateur)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE login ADD CONSTRAINT FK_AA08CB1050EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3C0E1077A FOREIGN KEY (id_profil) REFERENCES profil (id_profil)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE login DROP FOREIGN KEY FK_AA08CB1050EAE44');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3C0E1077A');
        $this->addSql('DROP TABLE login');
        $this->addSql('DROP TABLE profil');
        $this->addSql('DROP TABLE utilisateur');
    }
}
