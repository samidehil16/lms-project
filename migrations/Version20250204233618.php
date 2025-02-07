<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20250204233618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie_course (categorie_id INT NOT NULL, course_id INT NOT NULL, INDEX IDX_90B8C580BCF5E72D (categorie_id), INDEX IDX_90B8C580591CC992 (course_id), PRIMARY KEY(categorie_id, course_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE categorie_course ADD CONSTRAINT FK_90B8C580BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categorie_course ADD CONSTRAINT FK_90B8C580591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        
        $this->addSql('ALTER TABLE categorie_course DROP FOREIGN KEY FK_90B8C580BCF5E72D');
        $this->addSql('ALTER TABLE categorie_course DROP FOREIGN KEY FK_90B8C580591CC992');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE categorie_course');
    }
}
