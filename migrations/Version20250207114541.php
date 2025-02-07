<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250207114541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix is_read column in message table';
    }

    public function up(Schema $schema): void
    {
        // Vérifier si la colonne existe et la supprimer
        $this->addSql('
            SET @exist := (
                SELECT COUNT(*)
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = "message"
                AND COLUMN_NAME = "is_read"
            );
        ');
        
        $this->addSql('
            SET @query := IF(
                @exist > 0,
                "ALTER TABLE message DROP COLUMN is_read",
                "SELECT \'Column does not exist\'"
            );
        ');
        
        $this->addSql('PREPARE stmt FROM @query');
        $this->addSql('EXECUTE stmt');
        $this->addSql('DEALLOCATE PREPARE stmt');

        // Ajouter la nouvelle colonne
        $this->addSql('ALTER TABLE message ADD is_read TINYINT(1) NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE message DROP COLUMN is_read');
    }
} 