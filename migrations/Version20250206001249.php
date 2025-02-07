<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20250206001249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        
        $this->addSql('ALTER TABLE message ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE notification ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD is_read TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        
        $this->addSql('ALTER TABLE message DROP created_at');
        $this->addSql('ALTER TABLE notification DROP created_at, DROP is_read');
    }
}
