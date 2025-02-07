<?php

namespace App\DataFixtures;

use App\Entity\Chapter;
use App\Entity\Course;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class ChapterFixtures extends Fixture implements DependentFixtureInterface
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $courses = $manager->getRepository(Course::class)->findAll();

        if (empty($courses)) {
            return;
        }

        // Liste de vrais ID de vidéos YouTube pour des tutoriels de programmation
        $youtubeIds = [
            "OEV8gMkCHXQ",  // Symfony Tutorial
            "dH6VYRMRQFw",  // React Tutorial
            "1KtZKwuWbLw",  // PHP Tutorial
            "yRpLlJmRo2w",  // JavaScript Tutorial
            "3aCoZudPEKE",  // Docker Tutorial
            "pnhO8UaCgxg"   // Node.js Tutorial
        ];

        foreach ($courses as $course) {
            // Création de 5 à 10 chapitres par cours
            $numChapters = $faker->numberBetween(5, 10);
            
            for ($i = 1; $i <= $numChapters; $i++) {
                $chapter = new Chapter();
                
                // Création d'un titre structuré
                $title = sprintf(
                    "Chapitre %d : %s",
                    $i,
                    $faker->sentence(3)
                );

                // Création d'un contenu détaillé
                $content = implode("\n\n", [
                    "### Introduction",
                    $faker->paragraph(2),
                    "### Objectifs d'apprentissage",
                    "- " . implode("\n- ", $faker->sentences(3)),
                    "### Contenu principal",
                    $faker->paragraphs(3, true),
                    "### Résumé",
                    $faker->paragraph(),
                    "### Exercices pratiques",
                    "1. " . implode("\n2. ", $faker->sentences(3))
                ]);

                $chapter->setTitle($title)
                        ->setDescription($content)
                        ->setPosition(0)
                        ->setVideoUrl('https://www.youtube.com/watch?v=' . $faker->randomElement($youtubeIds))
                        ->setCourse($course);


                $manager->persist($chapter);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CourseFixtures::class,
        ];
    }
}
