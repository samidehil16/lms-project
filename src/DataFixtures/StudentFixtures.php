<?php

namespace App\DataFixtures;

use App\Entity\Student;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class StudentFixtures extends Fixture
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

        for ($i = 1; $i <= 10; $i++) {
            $student = new Student();
            
            // Copier une image d'exemple vers le dossier des avatars
            $sourceFile = __DIR__ . '/images/student' . $i . '.jpg';
            if (file_exists($sourceFile)) {
                $fileName = 'student' . $i . '_' . uniqid() . '.jpg';
                copy($sourceFile, $this->avatarsDirectory . '/' . $fileName);
                $student->setAvatar($fileName);
            }

            $student->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setEmail("student$i@example.com")
                ->setRoles(['ROLE_STUDENT'])
                ->setUpdatedAt(new \DateTimeImmutable());

            $password = $this->hasher->hashPassword($student, 'password');
            $student->setPassword($password);

            $manager->persist($student);
        }

        $manager->flush();
    }
}
