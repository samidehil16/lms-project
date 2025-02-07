<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250207104405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE instructor ADD biography LONGTEXT DEFAULT NULL, ADD speciality VARCHAR(255) DEFAULT NULL, DROP bio, DROP experience, DROP specialties, DROP social_links');
        $this->addSql('ALTER TABLE user ADD avatar VARCHAR(255) DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE instructor ADD bio LONGTEXT NOT NULL, ADD experience INT NOT NULL, ADD specialties JSON NOT NULL, ADD social_links JSON NOT NULL, DROP biography, DROP speciality');
        $this->addSql('ALTER TABLE user DROP avatar, DROP updated_at');
    }
}
