<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250207114540 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update schema with missing columns';
    }

    public function up(Schema $schema): void
    {
        // Désactiver la vérification des clés étrangères
        $this->addSql('SET FOREIGN_KEY_CHECKS=0');

        // Modifications de la table user
        $this->addSql('ALTER TABLE user CHANGE user_type type VARCHAR(255) NOT NULL');

        // Modifications de la table payment
        $this->addSql('ALTER TABLE payment 
            ADD payment_method VARCHAR(255) NOT NULL,
            ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            ADD transaction_id VARCHAR(255) DEFAULT NULL,
            ADD payment_details JSON DEFAULT NULL,
            CHANGE student_id student_id INT NOT NULL,
            CHANGE course_id course_id INT NOT NULL,
            CHANGE paid_at paid_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');

        // Modifications de la table question
        $this->addSql('ALTER TABLE question 
            ADD points INT NOT NULL,
            ADD type VARCHAR(255) NOT NULL,
            ADD correct_answer INT NOT NULL,
            CHANGE quiz_id quiz_id INT NOT NULL,
            CHANGE response choices JSON NOT NULL');

        // Modifications de la table quiz
        $this->addSql('ALTER TABLE quiz 
            ADD description LONGTEXT NOT NULL,
            ADD duration INT NOT NULL,
            ADD minimum_score INT NOT NULL,
            ADD is_published TINYINT(1) NOT NULL,
            ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            CHANGE course_id course_id INT NOT NULL');

        // Réactiver la vérification des clés étrangères
        $this->addSql('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(Schema $schema): void
    {
        // Désactiver la vérification des clés étrangères
        $this->addSql('SET FOREIGN_KEY_CHECKS=0');

        // Annuler les modifications de la table user
        $this->addSql('ALTER TABLE user CHANGE type user_type VARCHAR(255) NOT NULL');

        // Annuler les modifications de la table payment
        $this->addSql('ALTER TABLE payment 
            DROP payment_method,
            DROP created_at,
            DROP transaction_id,
            DROP payment_details');

        // Annuler les modifications de la table question
        $this->addSql('ALTER TABLE question 
            DROP points,
            DROP type,
            DROP correct_answer,
            CHANGE choices response JSON NOT NULL');

        // Annuler les modifications de la table quiz
        $this->addSql('ALTER TABLE quiz 
            DROP description,
            DROP duration,
            DROP minimum_score,
            DROP is_published,
            DROP created_at');

        // Réactiver la vérification des clés étrangères
        $this->addSql('SET FOREIGN_KEY_CHECKS=1');
    }
} 