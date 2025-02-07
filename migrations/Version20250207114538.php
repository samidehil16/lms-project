<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250207114538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Drop and recreate is_read column';
    }

    public function up(Schema $schema): void
    {
        // Désactiver la vérification des clés étrangères
        $this->addSql('SET FOREIGN_KEY_CHECKS=0');

        // Supprimer la colonne is_read si elle existe
        $this->addSql('ALTER TABLE message DROP COLUMN IF EXISTS is_read');
        
        // Recréer la colonne is_read
        $this->addSql('ALTER TABLE message ADD is_read TINYINT(1) NOT NULL DEFAULT 0');

        // Réactiver la vérification des clés étrangères
        $this->addSql('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(Schema $schema): void
    {
        // Désactiver la vérification des clés étrangères
        $this->addSql('SET FOREIGN_KEY_CHECKS=0');

        // Supprimer la colonne is_read
        $this->addSql('ALTER TABLE message DROP COLUMN IF EXISTS is_read');

        // Réactiver la vérification des clés étrangères
        $this->addSql('SET FOREIGN_KEY_CHECKS=1');
    }
} 