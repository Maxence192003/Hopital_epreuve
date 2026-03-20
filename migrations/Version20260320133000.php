<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration: Add created_at field to note_medical table
 */
final class Version20260320133000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add created_at field to note_medical table for tracking note creation date';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE note_medical ADD created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE note_medical DROP COLUMN created_at');
    }
}
