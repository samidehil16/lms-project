<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixtures extends Fixture
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
        $faker = Factory::create('fr_FR');

        // Admin User
        $admin = new User();
        
        // Copier une image d'exemple pour l'admin
        $sourceFile = __DIR__ . '/images/admin.jpg';
        if (file_exists($sourceFile)) {
            $fileName = 'admin_' . uniqid() . '.jpg';
            copy($sourceFile, $this->avatarsDirectory . '/' . $fileName);
            $admin->setAvatar($fileName);
        }

        $admin->setFirstName('Admin')
            ->setLastName('System')
            ->setEmail('admin@example.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setUpdatedAt(new \DateTimeImmutable());

        $password = $this->hasher->hashPassword($admin, 'password');
        $admin->setPassword($password);

        $manager->persist($admin);

        // Super Admin User
        $superAdmin = new User();
        $superAdmin->setFirstName('Super')
            ->setLastName('Admin')
            ->setEmail('superadmin@example.com')
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setUpdatedAt(new \DateTimeImmutable());

        $password = $this->hasher->hashPassword($superAdmin, 'password');
        $superAdmin->setPassword($password);

        $manager->persist($superAdmin);

        $manager->flush();
    }
}
