<?php

namespace App\DataFixtures;

use App\Entity\Question;
use App\Entity\Quiz;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class QuestionFixtures extends Fixture implements DependentFixtureInterface
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $quizzes = $manager->getRepository(Quiz::class)->findAll();

        if (empty($quizzes)) {
            return;
        }

        // Questions types avec leurs modèles de réponses
        $questionTemplates = [
            'multiple_choice' => [
                'templates' => [
                    'Quelle est la meilleure pratique pour %s ?',
                    'Parmi les options suivantes, laquelle %s ?',
                    'Quel est le principal avantage de %s ?'
                ],
                'subjects' => [
                    'la gestion des dépendances dans Symfony',
                    'l\'utilisation des services dans une application',
                    'l\'implémentation du pattern Repository',
                    'la sécurisation d\'une API REST',
                    'l\'optimisation des performances'
                ]
            ],
            'true_false' => [
                'templates' => [
                    'Est-il vrai que %s ?',
                    'Est-ce correct d\'affirmer que %s ?',
                    'Peut-on dire que %s ?'
                ],
                'subjects' => [
                    'Symfony utilise Doctrine par défaut',
                    'les controllers doivent toujours être légers',
                    'les services sont automatiquement injectés',
                    'le cache améliore toujours les performances'
                ]
            ]
        ];

        foreach ($quizzes as $quiz) {
            // 5 à 10 questions par quiz
            $numQuestions = $faker->numberBetween(5, 10);
            
            for ($i = 0; $i < $numQuestions; $i++) {
                $question = new Question();
                
                // Choisir aléatoirement le type de question
                $type = $faker->randomElement(['multiple_choice', 'true_false']);
                $template = $faker->randomElement($questionTemplates[$type]['templates']);
                $subject = $faker->randomElement($questionTemplates[$type]['subjects']);
                
                // Générer le contenu de la question
                $content = sprintf($template, $subject);
                
                // Générer les réponses selon le type
                if ($type === 'multiple_choice') {
                    $choices = [];
                    $correctAnswer = $faker->numberBetween(0, 3);
                    
                    for ($j = 0; $j < 4; $j++) {
                        $choices[] = $faker->unique()->sentence();
                    }
                    
                    $question->setChoices($choices)
                            ->setCorrectAnswer($correctAnswer);
                } else { // true_false
                    $question->setChoices(['Vrai', 'Faux'])
                            ->setCorrectAnswer($faker->boolean ? 0 : 1);
                }

                $question->setContent($content)
                    ->setType($type)
                    ->setQuiz($quiz)
                    ->setPoints($faker->randomElement([1, 2, 3, 5]));

                $manager->persist($question);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            QuizFixtures::class,
        ];
    }
}
