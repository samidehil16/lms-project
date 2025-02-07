<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250207113942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message ADD is_read TINYINT(1) NOT NULL, CHANGE sender_id sender_id INT NOT NULL, CHANGE receiver_id receiver_id INT NOT NULL');
        $this->addSql('ALTER TABLE payment ADD payment_method VARCHAR(255) NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD transaction_id VARCHAR(255) DEFAULT NULL, ADD payment_details JSON DEFAULT NULL, CHANGE student_id student_id INT NOT NULL, CHANGE course_id course_id INT NOT NULL, CHANGE paid_at paid_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE question ADD points INT NOT NULL, ADD type VARCHAR(255) NOT NULL, ADD correct_answer INT NOT NULL, CHANGE quiz_id quiz_id INT NOT NULL, CHANGE response choices JSON NOT NULL');
        $this->addSql('ALTER TABLE quiz ADD description LONGTEXT NOT NULL, ADD duration INT NOT NULL, ADD minimum_score INT NOT NULL, ADD is_published TINYINT(1) NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE course_id course_id INT NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE user_type type VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz DROP description, DROP duration, DROP minimum_score, DROP is_published, DROP created_at, CHANGE course_id course_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE message DROP is_read, CHANGE sender_id sender_id INT DEFAULT NULL, CHANGE receiver_id receiver_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE question DROP points, DROP type, DROP correct_answer, CHANGE quiz_id quiz_id INT DEFAULT NULL, CHANGE choices response JSON NOT NULL');
        $this->addSql('ALTER TABLE payment DROP payment_method, DROP created_at, DROP transaction_id, DROP payment_details, CHANGE student_id student_id INT DEFAULT NULL, CHANGE course_id course_id INT DEFAULT NULL, CHANGE paid_at paid_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user CHANGE type user_type VARCHAR(255) NOT NULL');
    }
}
