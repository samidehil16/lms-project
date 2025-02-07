<?php

namespace App\DataFixtures;

use App\Entity\Enrollment;
use App\Entity\Course;
use App\Entity\Student;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EnrollmentFixtures extends Fixture implements DependentFixtureInterface
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $students = $manager->getRepository(Student::class)->findAll();
        $courses = $manager->getRepository(Course::class)->findAll();

        foreach ($students as $student) {
            // Chaque étudiant s'inscrit à 2-4 cours aléatoires
            $randomCourses = $faker->randomElements($courses, $faker->numberBetween(2, 4));
            
            foreach ($randomCourses as $course) {
                // Créer l'inscription
                $enrollment = new Enrollment();
                $enrollment->setStudent($student)
                    ->setCourse($course)
                    ->setEnrolledAt(new \DateTimeImmutable());
                
                $manager->persist($enrollment);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            StudentFixtures::class,
            CourseFixtures::class,
        ];
    }
} 