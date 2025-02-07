<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250207121419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Reset and recreate complete database schema';
    }

    public function up(Schema $schema): void
    {
        // Désactiver les clés étrangères
        $this->addSql('SET FOREIGN_KEY_CHECKS=0');

        // Supprimer toutes les tables existantes
        $this->addSql('DROP TABLE IF EXISTS message');
        $this->addSql('DROP TABLE IF EXISTS notification');
        $this->addSql('DROP TABLE IF EXISTS payment');
        $this->addSql('DROP TABLE IF EXISTS question');
        $this->addSql('DROP TABLE IF EXISTS quiz');
        $this->addSql('DROP TABLE IF EXISTS course');
        $this->addSql('DROP TABLE IF EXISTS course_categorie');
        $this->addSql('DROP TABLE IF EXISTS categorie');
        $this->addSql('DROP TABLE IF EXISTS user');
        $this->addSql('DROP TABLE IF EXISTS student');
        $this->addSql('DROP TABLE IF EXISTS instructor');

        // Créer les tables avec la structure complète
        $this->addSql('CREATE TABLE user (
            id INT AUTO_INCREMENT NOT NULL,
            email VARCHAR(180) NOT NULL,
            roles JSON NOT NULL,
            password VARCHAR(255) NOT NULL,
            firstname VARCHAR(255) NOT NULL,
            lastname VARCHAR(255) NOT NULL,
            type VARCHAR(255) NOT NULL,
            UNIQUE INDEX UNIQ_8D93D649E7927C74 (email),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE student (
            id INT NOT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE instructor (
            id INT NOT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE categorie (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(255) NOT NULL,
            description LONGTEXT DEFAULT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE course (
            id INT AUTO_INCREMENT NOT NULL,
            instructor_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description LONGTEXT NOT NULL,
            price DOUBLE PRECISION NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            thumbnail VARCHAR(255) DEFAULT NULL,
            INDEX IDX_169E6FB98C4FC193 (instructor_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE course_categorie (
            course_id INT NOT NULL,
            categorie_id INT NOT NULL,
            INDEX IDX_E992E7E2591CC992 (course_id),
            INDEX IDX_E992E7E2BCF5E72D (categorie_id),
            PRIMARY KEY(course_id, categorie_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE message (
            id INT AUTO_INCREMENT NOT NULL,
            sender_id INT NOT NULL,
            receiver_id INT NOT NULL,
            content LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            is_read TINYINT(1) NOT NULL DEFAULT 0,
            INDEX IDX_B6BD307FF624B39D (sender_id),
            INDEX IDX_B6BD307FCD53EDB6 (receiver_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE notification (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            content LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            is_read TINYINT(1) NOT NULL,
            type VARCHAR(255) NOT NULL,
            INDEX IDX_BF5476CAA76ED395 (user_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE payment (
            id INT AUTO_INCREMENT NOT NULL,
            student_id INT NOT NULL,
            course_id INT NOT NULL,
            price DOUBLE PRECISION NOT NULL,
            status VARCHAR(255) NOT NULL,
            payment_method VARCHAR(255) NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            paid_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            transaction_id VARCHAR(255) DEFAULT NULL,
            payment_details JSON DEFAULT NULL,
            INDEX IDX_6D28840DCB944F1A (student_id),
            INDEX IDX_6D28840D591CC992 (course_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE question (
            id INT AUTO_INCREMENT NOT NULL,
            quiz_id INT NOT NULL,
            content LONGTEXT NOT NULL,
            choices JSON NOT NULL,
            points INT NOT NULL,
            type VARCHAR(255) NOT NULL,
            correct_answer INT NOT NULL,
            INDEX IDX_B6F7494E853CD175 (quiz_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE quiz (
            id INT AUTO_INCREMENT NOT NULL,
            course_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description LONGTEXT NOT NULL,
            duration INT NOT NULL,
            minimum_score INT NOT NULL,
            is_published TINYINT(1) NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            INDEX IDX_A412FA92591CC992 (course_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Ajouter les contraintes de clés étrangères
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE instructor ADD CONSTRAINT FK_31FC43D9BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB98C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id)');
        $this->addSql('ALTER TABLE course_categorie ADD CONSTRAINT FK_E992E7E2591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_categorie ADD CONSTRAINT FK_E992E7E2BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DCB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');

        // Réactiver les clés étrangères
        $this->addSql('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(Schema $schema): void
    {
        // Désactiver les clés étrangères
        $this->addSql('SET FOREIGN_KEY_CHECKS=0');

        // Supprimer toutes les tables
        $this->addSql('DROP TABLE IF EXISTS message');
        $this->addSql('DROP TABLE IF EXISTS notification');
        $this->addSql('DROP TABLE IF EXISTS payment');
        $this->addSql('DROP TABLE IF EXISTS question');
        $this->addSql('DROP TABLE IF EXISTS quiz');
        $this->addSql('DROP TABLE IF EXISTS course');
        $this->addSql('DROP TABLE IF EXISTS course_categorie');
        $this->addSql('DROP TABLE IF EXISTS categorie');
        $this->addSql('DROP TABLE IF EXISTS student');
        $this->addSql('DROP TABLE IF EXISTS instructor');
        $this->addSql('DROP TABLE IF EXISTS user');

        // Réactiver les clés étrangères
        $this->addSql('SET FOREIGN_KEY_CHECKS=1');
    }
}