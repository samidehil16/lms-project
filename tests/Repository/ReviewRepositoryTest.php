<?php

namespace App\Tests\Repository;

use App\Entity\Course;
use App\Entity\Review;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class ReviewRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testCalculateAverageRating(): void
    {
        // Créer un cours de test avec tous les champs requis
        $course = new Course();
        $course->setTitle('Cours de test')
               ->setDescription('Description du cours de test')
               ->setDuration('2 heures');
        
        $this->entityManager->persist($course);

        // Créer un utilisateur de test avec tous les champs requis
        $user = new User();
        $user->setEmail('test@test.com')
             ->setPassword('password123')
             ->setRoles(['ROLE_USER'])
             ->setFirstname('John')
             ->setLastname('Doe');
        
        $this->entityManager->persist($user);

        // Créer quelques reviews
        $ratings = [4, 5, 3, 5, 4];
        foreach ($ratings as $rating) {
            $review = new Review();
            $review->setRating($rating)
                  ->setComment('Test comment')
                  ->setCourse($course)
                  ->setUser($user)
                  ->setIsApproved(true)
                  ->setCreatedAt(new \DateTimeImmutable());
            
            $this->entityManager->persist($review);
        }

        $this->entityManager->flush();

        // Calculer la moyenne
        $reviewRepository = $this->entityManager->getRepository(Review::class);
        $result = $reviewRepository->calculateAverageRating($course);

        // Vérifier les résultats
        $this->assertEquals(5, $result['reviewCount']);
        $this->assertEquals(4.20, number_format($result['averageRating'], 2));

        // Nettoyer la base de données
        foreach ($course->getReviews() as $review) {
            $this->entityManager->remove($review);
        }
        $this->entityManager->remove($course);
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        if ($this->entityManager) {
            $this->entityManager->close();
            $this->entityManager = null;
        }
    }
} 