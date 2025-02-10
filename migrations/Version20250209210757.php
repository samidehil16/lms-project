<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250209210757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE question ADD type VARCHAR(255) NOT NULL, ADD points INT NOT NULL');
        $this->addSql('ALTER TABLE quiz DROP INDEX FK_A412FA92579F4768, ADD UNIQUE INDEX UNIQ_A412FA92579F4768 (chapter_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE question DROP type, DROP points');
        $this->addSql('ALTER TABLE quiz DROP INDEX UNIQ_A412FA92579F4768, ADD INDEX FK_A412FA92579F4768 (chapter_id)');
    }
}
