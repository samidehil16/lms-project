<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20250205222227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
       
        $this->addSql('ALTER TABLE instructor ADD experience INT NOT NULL, ADD specialties JSON NOT NULL, ADD social_links JSON NOT NULL');
        $this->addSql('ALTER TABLE user ADD firstname VARCHAR(50) NOT NULL, ADD lastname VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        
        $this->addSql('ALTER TABLE user DROP firstname, DROP lastname');
        $this->addSql('ALTER TABLE instructor DROP experience, DROP specialties, DROP social_links');
    }
}
