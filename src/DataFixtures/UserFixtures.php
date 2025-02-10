<?php

namespace App\DataFixtures;

use App\Entity\Student;
use App\Entity\Instructor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private string $projectDir
    ) {}

    public function load(ObjectManager $manager): void
    {
        // Création des instructeurs
        $instructors = [
            [
                'email' => 'john.doe@example.com',
                'firstname' => 'John',
                'lastname' => 'Doe',
                'speciality' => 'Développeur Full Stack Senior',
                'biography' => 'Expert en développement web avec 15 ans d\'expérience. Ancien architecte technique chez Google et Amazon.',
                'ref' => 'instructor_1'
            ],
            [
                'email' => 'marie.dupont@example.com',
                'firstname' => 'Marie',
                'lastname' => 'Dupont',
                'speciality' => 'UX/UI Designer Senior',
                'biography' => 'Designer UX/UI avec 10 ans d\'expérience dans la création d\'interfaces utilisateur.',
                'ref' => 'instructor_2'
            ],
            [
                'email' => 'david.smith@example.com',
                'firstname' => 'David',
                'lastname' => 'Smith',
                'speciality' => 'Expert en Cybersécurité',
                'biography' => 'Consultant en cybersécurité avec plus de 12 ans d\'expérience.',
                'ref' => 'instructor_3'
            ]
        ];

        foreach ($instructors as $instructorData) {
            $instructor = new Instructor();
            $instructor->setEmail($instructorData['email'])
                ->setFirstname($instructorData['firstname'])
                ->setLastname($instructorData['lastname'])
                ->setSpeciality($instructorData['speciality'])
                ->setBiography($instructorData['biography'])
                ->setPassword($this->passwordHasher->hashPassword($instructor, 'password123'));
            
            $manager->persist($instructor);
            $this->addReference($instructorData['ref'], $instructor);
        }

        // Création des étudiants
        $students = [
            [
                'email' => 'etudiant1@example.com',
                'firstname' => 'Alice',
                'lastname' => 'Martin',
                'ref' => 'student_1'
            ],
            [
                'email' => 'etudiant2@example.com',
                'firstname' => 'Lucas',
                'lastname' => 'Bernard',
                'ref' => 'student_2'
            ],
            [
                'email' => 'etudiant3@example.com',
                'firstname' => 'Emma',
                'lastname' => 'Dubois',
                'ref' => 'student_3'
            ],
            [
                'email' => 'etudiant4@example.com',
                'firstname' => 'Hugo',
                'lastname' => 'Thomas',
                'ref' => 'student_4'
            ],
            [
                'email' => 'etudiant5@example.com',
                'firstname' => 'Léa',
                'lastname' => 'Robert',
                'ref' => 'student_5'
            ]
        ];

        foreach ($students as $studentData) {
            $student = new Student();
            $student->setEmail($studentData['email'])
                ->setFirstname($studentData['firstname'])
                ->setLastname($studentData['lastname'])
                ->setPassword($this->passwordHasher->hashPassword($student, 'password123'));
            
            $manager->persist($student);
            $this->addReference($studentData['ref'], $student);
        }

        $manager->flush();
    }
}
