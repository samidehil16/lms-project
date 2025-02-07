<?php

namespace App\DataFixtures;

use App\Entity\Quiz;
use App\Entity\Course;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class QuizFixtures extends Fixture implements DependentFixtureInterface
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

        $quizTypes = [
            'evaluation' => [
                'title' => [
                    'Évaluation finale : %s',
                    'Test de connaissances : %s',
                    'Quiz de validation : %s'
                ],
                'description' => [
                    'Évaluez vos connaissances sur %s. Ce quiz final couvre tous les aspects importants du cours.',
                    'Testez votre compréhension de %s. Cette évaluation vous permettra de valider vos acquis.',
                    'Vérifiez votre maîtrise de %s à travers ce quiz complet.'
                ]
            ],
            'practice' => [
                'title' => [
                    'Exercices pratiques : %s',
                    'Quiz d\'entraînement : %s',
                    'Pratique guidée : %s'
                ],
                'description' => [
                    'Pratiquez vos compétences en %s avec ces exercices interactifs.',
                    'Renforcez votre apprentissage de %s grâce à ce quiz d\'entraînement.',
                    'Mettez en pratique vos connaissances sur %s.'
                ]
            ],
            'chapter' => [
                'title' => [
                    'Quiz du chapitre : %s',
                    'Révision : %s',
                    'Mini-évaluation : %s'
                ],
                'description' => [
                    'Validez votre compréhension du chapitre sur %s.',
                    'Révisez les concepts clés de %s avec ce quiz rapide.',
                    'Vérifiez vos connaissances sur %s avant de continuer.'
                ]
            ]
        ];

        foreach ($courses as $course) {
            // 2 à 4 quiz par cours
            $numQuizzes = $faker->numberBetween(2, 4);
            
            for ($i = 0; $i < $numQuizzes; $i++) {
                $quiz = new Quiz();
                
                // Choisir un type de quiz
                $type = $faker->randomElement(array_keys($quizTypes));
                $titleTemplate = $faker->randomElement($quizTypes[$type]['title']);
                $descTemplate = $faker->randomElement($quizTypes[$type]['description']);
                
                // Générer un sujet spécifique pour ce quiz
                $subject = $faker->randomElement([
                    'les fondamentaux',
                    'les concepts avancés',
                    'la mise en pratique',
                    'l\'optimisation',
                    'les bonnes pratiques'
                ]);

                $quiz->setTitle(sprintf($titleTemplate, $subject))
                    ->setDescription(sprintf($descTemplate, $subject))
                    ->setCourse($course)
                    ->setDuration($faker->randomElement([15, 20, 30, 45, 60])) // durée en minutes
                    ->setMinimumScore($faker->randomElement([60, 70, 80])) // score minimum en pourcentage
                    ->setCreatedAt(new \DateTimeImmutable($faker->dateTimeBetween('-6 months')->format('Y-m-d H:i:s')))
                    ->setIsPublished($faker->boolean(80)); // 80% de chances d'être publié

                $manager->persist($quiz);
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
