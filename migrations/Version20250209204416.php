<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250209204416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chapter ADD content LONGTEXT NOT NULL, DROP description, DROP duration');
        $this->addSql('ALTER TABLE quiz DROP INDEX FK_A412FA92579F4768, ADD UNIQUE INDEX UNIQ_A412FA92579F4768 (chapter_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chapter ADD description LONGTEXT DEFAULT NULL, ADD duration VARCHAR(255) DEFAULT NULL, DROP content');
        $this->addSql('ALTER TABLE quiz DROP INDEX UNIQ_A412FA92579F4768, ADD INDEX FK_A412FA92579F4768 (chapter_id)');
    }
}
