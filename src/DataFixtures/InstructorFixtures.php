<?php

namespace App\DataFixtures;

use App\Entity\Instructor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;
use Symfony\Component\HttpFoundation\File\File;

class InstructorFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    private string $avatarsDirectory;

    public function __construct(UserPasswordHasherInterface $hasher, string $projectDir)
    {
        $this->hasher = $hasher;
        $this->avatarsDirectory = $projectDir . '/public/uploads/avatars';
    }

    public function load(ObjectManager $manager): void
    {
        // Créer le dossier s'il n'existe pas
        if (!file_exists($this->avatarsDirectory)) {
            mkdir($this->avatarsDirectory, 0777, true);
        }

        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= 5; $i++) {
            $instructor = new Instructor();
            
            // Copier une image d'exemple vers le dossier des avatars
            $sourceFile = __DIR__ . '/images/instructor' . $i . '.jpg';
            if (file_exists($sourceFile)) {
                $fileName = 'instructor' . $i . '_' . uniqid() . '.jpg';
                copy($sourceFile, $this->avatarsDirectory . '/' . $fileName);
                $instructor->setAvatar($fileName);
            }

            $instructor->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setEmail("instructor$i@example.com")
                ->setRoles(['ROLE_INSTRUCTOR'])
                ->setBiography($faker->paragraphs(2, true))
                ->setSpeciality($faker->randomElement(['Développement Web', 'Design', 'Marketing', 'Data Science']))
                ->setUpdatedAt(new \DateTimeImmutable());

            $password = $this->hasher->hashPassword($instructor, 'password');
            $instructor->setPassword($password);

            $manager->persist($instructor);
        }

        $manager->flush();
    }
}
