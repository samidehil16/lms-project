<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250207114544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create complete database schema';
    }

    public function up(Schema $schema): void
    {
        // Désactiver les clés étrangères
        $this->addSql('SET FOREIGN_KEY_CHECKS=0');

        // Supprimer les tables si elles existent
        $this->addSql('DROP TABLE IF EXISTS course_categorie');
        $this->addSql('DROP TABLE IF EXISTS payment');
        $this->addSql('DROP TABLE IF EXISTS question');
        $this->addSql('DROP TABLE IF EXISTS quiz');
        $this->addSql('DROP TABLE IF EXISTS message');
        $this->addSql('DROP TABLE IF EXISTS notification');
        $this->addSql('DROP TABLE IF EXISTS course');
        $this->addSql('DROP TABLE IF EXISTS categorie');
        $this->addSql('DROP TABLE IF EXISTS student');
        $this->addSql('DROP TABLE IF EXISTS instructor');
        $this->addSql('DROP TABLE IF EXISTS user');

        // Créer les tables avec la structure complète
        // ... [Le reste du code de création des tables reste identique à la version précédente]
    }

    public function down(Schema $schema): void
    {
        // ... [Le code down reste identique]
    }
} 