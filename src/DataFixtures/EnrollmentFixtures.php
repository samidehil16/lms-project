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

        $enrollmentData = [
            [
                'student_ref' => 'student_1',
                'course_ref' => 'course_1',
                'completed_chapters' => ['chapter_1_1', 'chapter_1_2'],
                'ref' => 'enrollment_1_1'
            ],
            [
                'student_ref' => 'student_2',
                'course_ref' => 'course_2',
                'completed_chapters' => ['chapter_2_1'],
                'ref' => 'enrollment_2_1'
            ],
            // ... autres inscriptions avec leurs refs
        ];

        foreach ($enrollmentData as $data) {
            $enrollment = new Enrollment();
            $enrollment->setStudent($this->getReference($data['student_ref'], Student::class))
                ->setCourse($this->getReference($data['course_ref'], Course::class))
                ->setCompletedChapters($data['completed_chapters'])
                ->setProgressPercentage(0)
                ->setEnrolledAt(new \DateTimeImmutable());

            $manager->persist($enrollment);
            $this->addReference($data['ref'], $enrollment);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CourseFixtures::class,
            ChapterFixtures::class
        ];
    }
} 