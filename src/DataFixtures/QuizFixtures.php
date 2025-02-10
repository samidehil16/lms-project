<?php

namespace App\DataFixtures;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\Chapter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class QuizFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private string $projectDir)
    {
        // Le constructeur avec $projectDir injecté par Symfony
    }

    public function load(ObjectManager $manager): void
    {
        $quizData = [
            'chapter_1_1' => [
                'title' => 'Quiz - Introduction au développement web',
                'description' => 'Testez vos connaissances sur les fondamentaux du web',
                'duration' => 30,
                'minimum_score' => 70,
                'ref' => 'quiz_1_1',
                'questions' => [
                    [
                        'content' => 'Quel protocole est utilisé pour le web ?',
                        'choices' => ['HTTP', 'FTP', 'SMTP'],
                        'correct_answer' => 0,
                        'points' => 1,
                        'type' => 'multiple_choice',
                        'ref' => 'question_1_1_1'
                    ],
                    [
                        'content' => 'Quelle balise HTML est utilisée pour les titres principaux ?',
                        'choices' => ['h1', 'p', 'div'],
                        'correct_answer' => 0,
                        'points' => 1,
                        'type' => 'multiple_choice',
                        'ref' => 'question_1_1_2'
                    ],
                    [
                        'content' => 'CSS est utilisé pour :',
                        'choices' => ['Le style', 'La logique', 'Les données'],
                        'correct_answer' => 0,
                        'points' => 1,
                        'type' => 'multiple_choice',
                        'ref' => 'question_1_1_3'
                    ]
                ]
            ],
            'chapter_1_2' => [
                'title' => 'Quiz - HTML et Structure',
                'description' => 'Vérifiez votre compréhension de HTML',
                'duration' => 20,
                'minimum_score' => 60,
                'ref' => 'quiz_1_2',
                'questions' => [
                    [
                        'content' => 'Quelle balise crée un lien hypertexte ?',
                        'choices' => ['a', 'link', 'href'],
                        'correct_answer' => 0,
                        'points' => 1,
                        'type' => 'multiple_choice',
                        'ref' => 'question_1_2_1'
                    ],
                    [
                        'content' => 'Comment définir un paragraphe en HTML ?',
                        'choices' => ['p', 'para', 'text'],
                        'correct_answer' => 0,
                        'points' => 1,
                        'type' => 'multiple_choice',
                        'ref' => 'question_1_2_2'
                    ]
                ]
            ]
        ];

        foreach ($quizData as $chapterRef => $quiz) {
            $quizEntity = new Quiz();
            $chapter = $this->getReference($chapterRef, Chapter::class);
            $course = $chapter->getCourse();
            
            $quizEntity->setTitle($quiz['title'])
                ->setDescription($quiz['description'])
                ->setDuration($quiz['duration'])
                ->setMinimumScore($quiz['minimum_score'])
                ->setChapter($chapter)
                ->setCourse($course);
            
            $this->addReference($quiz['ref'], $quizEntity);

            foreach ($quiz['questions'] as $questionData) {
                $question = new Question();
                $question->setContent($questionData['content'])
                    ->setChoices($questionData['choices'])
                    ->setCorrectAnswer($questionData['correct_answer'])
                    ->setPoints($questionData['points'])
                    ->setType($questionData['type'])
                    ->setQuiz($quizEntity);
                
                $manager->persist($question);
                $this->addReference($questionData['ref'], $question);
            }
            
            $manager->persist($quizEntity);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ChapterFixtures::class,
        ];
    }
}
