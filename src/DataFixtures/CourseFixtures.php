<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Course;
use App\Entity\Instructor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class CourseFixtures extends Fixture implements DependentFixtureInterface
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $categories = $manager->getRepository(Categorie::class)->findAll();
        $instructors = $manager->getRepository(Instructor::class)->findAll();

        // Créer le dossier pour les thumbnails s'il n'existe pas
        $thumbnailsDirectory = $this->projectDir . '/public/uploads/courses';
        if (!file_exists($thumbnailsDirectory)) {
            mkdir($thumbnailsDirectory, 0777, true);
        }

        foreach ($categories as $category) {
            for ($i = 0; $i < 3; $i++) {
                $course = new Course();
                
                // Titre plus descriptif
                $title = $faker->randomElement([
                    "Maîtrisez ", 
                    "Apprenez ", 
                    "Formation complète ", 
                    "Guide pratique "
                ]) . $category->getName();

                // Description détaillée
                $description = implode("\n\n", [
                    "### À propos de ce cours",
                    $faker->paragraph(3),
                    "### Ce que vous allez apprendre",
                    "- " . implode("\n- ", $faker->sentences(4)),
                    "### Prérequis",
                    "- " . implode("\n- ", $faker->sentences(2)),
                    "### Pour qui est ce cours ?",
                    $faker->paragraph(2)
                ]);

                $course->setTitle($title)
                    ->setDescription($description)
                    ->setPrice($faker->randomFloat(2, 29.99, 199.99))
                    ->addCategory($category)
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setUpdatedAt(new \DateTimeImmutable())
                    ->setInstructor($faker->randomElement($instructors));

                // Copier une image d'exemple pour la thumbnail
                $sourceFile = __DIR__ . '/images/course' . ($i + 1) . '.jpg';
                if (file_exists($sourceFile)) {
                    $fileName = 'course_' . uniqid() . '.jpg';
                    copy($sourceFile, $thumbnailsDirectory . '/' . $fileName);
                    $course->setThumbnail($fileName);
                }

                $manager->persist($course);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            InstructorFixtures::class
        ];
    }
}
