<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250209215734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course ADD average_rating NUMERIC(3, 2) DEFAULT NULL, ADD reviews_count INT NOT NULL');
        $this->addSql('ALTER TABLE quiz ADD course_id INT NOT NULL, CHANGE chapter_id chapter_id INT DEFAULT NULL, CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('CREATE INDEX IDX_A412FA92591CC992 ON quiz (course_id)');
        $this->addSql('ALTER TABLE review ADD is_approved TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA92591CC992');
        $this->addSql('DROP INDEX IDX_A412FA92591CC992 ON quiz');
        $this->addSql('ALTER TABLE quiz DROP course_id, CHANGE chapter_id chapter_id INT NOT NULL, CHANGE description description LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE course DROP average_rating, DROP reviews_count');
        $this->addSql('ALTER TABLE review DROP is_approved');
    }
}
