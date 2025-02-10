<?php

namespace App\DataFixtures;

use App\Entity\Review;
use App\Entity\Course;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private string $projectDir)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $courses = $manager->getRepository(Course::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        if (empty($courses) || empty($users)) {
            return;
        }

        $comments = [
            'Excellent cours, très bien structuré !',
            'Les explications sont claires et précises.',
            'J\'ai beaucoup appris grâce à ce cours.',
            'Le formateur explique très bien les concepts.',
            'Parfait pour débuter dans le domaine.',
            'Les exercices pratiques sont très utiles.',
            'Je recommande vivement ce cours.',
            'Très bon rapport qualité/prix.',
            'Le contenu est à jour et pertinent.',
            'Les vidéos sont de bonne qualité.'
        ];

        // Pour chaque cours
        foreach ($courses as $course) {
            $numReviews = $faker->numberBetween(5, 15);
            $totalRating = 0;
            
            for ($i = 0; $i < $numReviews; $i++) {
                $review = new Review();
                $user = $faker->randomElement($users);
                $rating = $faker->numberBetween(3, 5);
                
                $totalRating += $rating;
                
                $review->setRating($rating)
                    ->setComment($faker->randomElement($comments))
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setCourse($course)
                    ->setUser($user)
                    ->setIsApproved(true);  // Par défaut, on approuve les avis

                $manager->persist($review);
            }

            // Mise à jour des statistiques du cours
            if ($numReviews > 0) {
                $averageRating = $totalRating / $numReviews;
                $course->setAverageRating(number_format($averageRating, 2))
                      ->setReviewsCount($numReviews);
                
                $manager->persist($course);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CourseFixtures::class,
            UserFixtures::class,
        ];
    }
} 