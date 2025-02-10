# E-Learning Platform

Une plateforme d'apprentissage en ligne permettant aux étudiants de suivre des cours et aux instructeurs de les créer. Le site propose une gestion complète des cours, des paiements et des inscriptions.

## 🚀 Installation

### Prérequis
- PHP 8.1 ou supérieur
- Composer
- Symfony CLI
- MySQL
- Node.js et npm

### Étapes d'installation

1. Cloner le projet

2. Installer les dépendances

composer install

npm install

3. Configurer la base de données dans .env

exemple:

DATABASE_URL="mysql://db_user:db_password@localhost:3306/db_name"

4. Créer la base de données

php bin/console doctrine:database:create

5. Exécuter les migrations

php bin/console doctrine:migrations:migrate

6. Charger les fixtures

php bin/console doctrine:fixtures:load

7. Lancer le serveur

symfony server:start

8. Accéder à l'application

http://localhost:8000   


## 🔑 Comptes de test

### Instructeurs
- Email: john.doe@example.com
- Mot de passe: password123
> Expert en développement web

- Email: marie.dupont@example.com
- Mot de passe: password123
> Experte en UX/UI Design

- Email: david.smith@example.com
- Mot de passe: password123
> Expert en Cybersécurité

### Étudiants
- Email: etudiant1@example.com
- Mot de passe: password123

## 🌐 Accès

- Site : http://localhost:8000
- Cours disponibles : http://localhost:8000/courses
- Connexion : http://localhost:8000/login

## 📱 Fonctionnalités

- Système d'authentification (Instructeur, Étudiant)
- Catalogue de cours avec filtres par catégories
- Chapitres vidéo pour chaque cours
- Système de paiement
- Gestion des inscriptions
- Interface instructeur pour gérer les cours

## 📝 Notes

- Les vidéos des cours sont hébergées sur YouTube
- Les paiements sont en mode test
- Les images sont stockées localement

## 🛠 Technologies utilisées

- Symfony 6
- Twig
- Doctrine
- MySQL
- Tailwind CSS


## 📦 Structure du projet

src/
├── Controller/
├── Entity/
├── Repository/
├── Security/
├── Service/
├── Form/
├── DataFixtures/
├── Templates/



