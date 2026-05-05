<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260429120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Synchronize database schema with current Doctrine mapping';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dossier_patient RENAME INDEX uniq_5880edd350eae44 TO UNIQ_58803ED350EAE44');
        $this->addSql('ALTER TABLE greffe CHANGE note_greffe note_greffe LONGTEXT DEFAULT NULL, CHANGE note_donneur note_donneur LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE greffe RENAME INDEX idx_greffe_dossier TO IDX_69FCC4BBB1DEFD5B');
        $this->addSql('ALTER TABLE note_medical CHANGE id_dossier_patient id_dossier_patient VARCHAR(50) NOT NULL, CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE note_medical RENAME INDEX idx_note_dossier TO IDX_F67DC3C0B1DEFD5B');
        $this->addSql('ALTER TABLE utilisateur RENAME INDEX id_login TO IDX_1D1C63B3448D8A20');
        $this->addSql('ALTER TABLE utilisateur RENAME INDEX id_profil TO IDX_1D1C63B3C0E1077A');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE utilisateur RENAME INDEX IDX_1D1C63B3C0E1077A TO id_profil');
        $this->addSql('ALTER TABLE utilisateur RENAME INDEX IDX_1D1C63B3448D8A20 TO id_login');
        $this->addSql('ALTER TABLE note_medical RENAME INDEX IDX_F67DC3C0B1DEFD5B TO idx_note_dossier');
        $this->addSql('ALTER TABLE note_medical CHANGE id_dossier_patient id_dossier_patient VARCHAR(50) DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE greffe RENAME INDEX IDX_69FCC4BBB1DEFD5B TO idx_greffe_dossier');
        $this->addSql('ALTER TABLE greffe CHANGE note_greffe note_greffe TEXT DEFAULT NULL, CHANGE note_donneur note_donneur TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE dossier_patient RENAME INDEX UNIQ_58803ED350EAE44 TO uniq_5880edd350eae44');
        $this->addSql('DROP TABLE messenger_messages');
    }
}