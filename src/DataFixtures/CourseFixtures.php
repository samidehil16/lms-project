<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Course;
use App\Entity\Instructor;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CourseFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private string $projectDir)
    {
        // Le constructeur avec $projectDir injecté par Symfony
    }

    public function load(ObjectManager $manager): void
    {
        $courses = [
            [
                'title' => 'Formation Complète Développeur Web Full Stack',
                'subtitle' => 'Maîtrisez HTML, CSS, JavaScript, PHP et les frameworks modernes',
                'description' => "Une formation complète pour devenir développeur web full stack professionnel.",
                'thumbnail' => '/images/courses/course1.jpg',
                'price' => 299.99,
                'instructor_ref' => 'instructor_1',
                'duration' => '50 heures',
                'prerequisites' => [
                    'Connaissances de base en informatique',
                    'Comprendre les concepts de base de la programmation',
                    'Un ordinateur avec accès internet'
                ],
                'learning_objectives' => [
                    'Maîtriser HTML5, CSS3 et JavaScript moderne',
                    'Créer des applications web responsive',
                    'Développer des API REST'
                ],
                'ref' => 'course_1',
                'category_refs' => ['category_dev_web', 'category_devops']
            ],
            [
                'title' => 'UX/UI Design Masterclass',
                'subtitle' => 'Créez des interfaces utilisateur modernes et intuitives',
                'description' => "Apprenez à concevoir des interfaces utilisateur exceptionnelles.",
                'thumbnail' => '/images/courses/course2.jpg',
                'price' => 199.99,
                'instructor_ref' => 'instructor_2',
                'duration' => '30 heures',
                'prerequisites' => [
                    'Aucune expérience en design requise',
                    'Intérêt pour l\'expérience utilisateur',
                    'Ordinateur avec Figma installé'
                ],
                'learning_objectives' => [
                    'Maîtriser les principes du design UX/UI',
                    'Créer des maquettes avec Figma',
                    'Concevoir des prototypes interactifs'
                ],
                'ref' => 'course_2',
                'category_refs' => ['category_design']
            ],
            [
                'title' => 'Cybersécurité : De Débutant à Expert',
                'subtitle' => 'Apprenez à sécuriser vos applications et systèmes',
                'description' => "Formation complète en cybersécurité.",
                'thumbnail' => '/images/courses/course3.jpg',
                'price' => 399.99,
                'instructor_ref' => 'instructor_3',
                'duration' => '40 heures',
                'prerequisites' => [
                    'Connaissances de base en réseaux',
                    'Familiarité avec Linux',
                    'Notions de programmation'
                ],
                'learning_objectives' => [
                    'Comprendre les menaces de sécurité courantes',
                    'Mettre en place des défenses efficaces',
                    'Réaliser des tests de pénétration'
                ],
                'ref' => 'course_3',
                'category_refs' => ['category_security', 'category_devops']
            ]
        ];

        // Copier les images depuis le dossier source vers le dossier public
        $sourceDir = __DIR__ . '/images/courses';
        $targetDir = $this->projectDir . '/public/images/courses';
        
        // Créer le dossier cible s'il n'existe pas
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Copier les images
        foreach (['course1.jpg', 'course2.jpg', 'course3.jpg'] as $image) {
            if (file_exists($sourceDir . '/' . $image)) {
                copy($sourceDir . '/' . $image, $targetDir . '/' . $image);
            }
        }

        foreach ($courses as $courseData) {
            $course = new Course();
            $course->setTitle($courseData['title'])
                ->setDescription($courseData['description'])
                ->setThumbnail($courseData['thumbnail'])
                ->setPrice($courseData['price'])
                ->setInstructor($this->getReference($courseData['instructor_ref'], Instructor::class))
                ->setDuration($courseData['duration'])
                ->setPrerequisites($courseData['prerequisites'])
                ->setLearningObjectives($courseData['learning_objectives'])
                ->setCreatedAt(new \DateTimeImmutable());

            // Ajout des catégories
            foreach ($courseData['category_refs'] as $categoryRef) {
                $course->addCategory($this->getReference($categoryRef, Categorie::class));
            }
            
            $manager->persist($course);
            $this->addReference($courseData['ref'], $course);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
