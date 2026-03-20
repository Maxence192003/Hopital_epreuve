<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260320101009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Invert Utilisateur->DossierPatient relationship: move FK from Utilisateur to DossierPatient';
    }

    public function up(Schema $schema): void
    {
        // Add id_utilisateur column to dossier_patient
        $this->addSql('ALTER TABLE dossier_patient ADD id_utilisateur INT DEFAULT NULL');
        
        // Add foreign key constraint from dossier_patient to utilisateur
        $this->addSql('ALTER TABLE dossier_patient ADD CONSTRAINT FK_5880EDD350EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
        
        // Create unique index on id_utilisateur (for OneToOne relationship)
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5880EDD350EAE44 ON dossier_patient (id_utilisateur)');
        
        // Drop the foreign key from utilisateur
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY utilisateur_ibfk_1');
        
        // Drop the id_dossier_patient column from utilisateur
        $this->addSql('ALTER TABLE utilisateur DROP INDEX id_dossier_patient');
        $this->addSql('ALTER TABLE utilisateur DROP COLUMN id_dossier_patient');
    }

    public function down(Schema $schema): void
    {
        // Add back id_dossier_patient column to utilisateur
        $this->addSql('ALTER TABLE utilisateur ADD id_dossier_patient VARCHAR(50) DEFAULT NULL');
        
        // Create unique index on id_dossier_patient
        $this->addSql('CREATE UNIQUE INDEX id_dossier_patient ON utilisateur (id_dossier_patient)');
        
        // Add back the foreign key
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT utilisateur_ibfk_1 FOREIGN KEY (id_dossier_patient) REFERENCES dossier_patient (id_dossier_patient)');
        
        // Drop the foreign key from dossier_patient
        $this->addSql('ALTER TABLE dossier_patient DROP FOREIGN KEY FK_5880EDD350EAE44');
        
        // Drop unique index from dossier_patient
        $this->addSql('DROP INDEX UNIQ_5880EDD350EAE44 ON dossier_patient');
        
        // Drop id_utilisateur column from dossier_patient
        $this->addSql('ALTER TABLE dossier_patient DROP COLUMN id_utilisateur');
    }
}
