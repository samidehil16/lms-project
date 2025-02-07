<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250207114537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Drop all foreign key constraints';
    }

    public function up(Schema $schema): void
    {
        // Désactiver la vérification des clés étrangères
        $this->addSql('SET FOREIGN_KEY_CHECKS=0');

        // Supprimer toutes les contraintes de clés étrangères
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FCD53EDB6');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D591CC992');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DCB944F1A');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA92591CC992');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E853CD175');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB98C4FC193');
        $this->addSql('ALTER TABLE course_categorie DROP FOREIGN KEY FK_E992E7E2591CC992');
        $this->addSql('ALTER TABLE course_categorie DROP FOREIGN KEY FK_E992E7E2BCF5E72D');

        // Réactiver la vérification des clés étrangères
        $this->addSql('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(Schema $schema): void
    {
        // Désactiver la vérification des clés étrangères
        $this->addSql('SET FOREIGN_KEY_CHECKS=0');

        // Recréer toutes les contraintes de clés étrangères
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DCB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB98C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id)');
        $this->addSql('ALTER TABLE course_categorie ADD CONSTRAINT FK_E992E7E2591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_categorie ADD CONSTRAINT FK_E992E7E2BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE');

        // Réactiver la vérification des clés étrangères
        $this->addSql('SET FOREIGN_KEY_CHECKS=1');
    }
} 