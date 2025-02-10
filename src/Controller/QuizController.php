<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Course;
use App\Entity\Chapter;
use App\Entity\Student;
use App\Entity\Enrollment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class QuizController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/course/{course_id}/chapter/{chapter_id}/quiz/{id}', name: 'app_quiz_start')]
    public function start(int $course_id, int $chapter_id, int $id): Response
    {
        $course = $this->entityManager->getRepository(Course::class)->find($course_id);
        $chapter = $this->entityManager->getRepository(Chapter::class)->find($chapter_id);
        $quiz = $this->entityManager->getRepository(Quiz::class)->find($id);

        if (!$course || !$chapter || !$quiz) {
            throw $this->createNotFoundException('Quiz non trouvé');
        }

        // Vérifier que l'utilisateur est un étudiant
        $user = $this->getUser();
        if (!$user instanceof Student) {
            throw $this->createAccessDeniedException('Accès réservé aux étudiants');
        }

        // Vérifier l'inscription au cours
        $enrollment = $this->entityManager->getRepository(Enrollment::class)->findOneBy([
            'student' => $user,
            'course' => $course
        ]);

        if (!$enrollment) {
            throw $this->createAccessDeniedException('Vous devez être inscrit au cours pour accéder au quiz');
        }

        // Vérifier que le quiz appartient bien au chapitre
        if ($quiz->getChapter() !== $chapter) {
            throw $this->createNotFoundException('Quiz non trouvé pour ce chapitre');
        }

        // Vérifier si le chapitre précédent est complété
        $chapters = $course->getChapters()->toArray();
        usort($chapters, fn($a, $b) => $a->getPosition() <=> $b->getPosition());
        $currentIndex = array_search($chapter, $chapters);
        
        if ($currentIndex > 0) {
            $previousChapter = $chapters[$currentIndex - 1];
            if (!in_array($previousChapter->getId(), $enrollment->getCompletedChapters())) {
                $this->addFlash('error', 'Vous devez compléter le chapitre précédent avant de passer ce quiz.');
                return $this->redirectToRoute('app_chapter_show', [
                    'course_id' => $course->getId(),
                    'id' => $previousChapter->getId()
                ]);
            }
        }

        // Récupérer les questions du quiz dans l'ordre
        $questions = $quiz->getQuestions()->toArray();
        usort($questions, fn($a, $b) => $a->getPosition() <=> $b->getPosition());

        return $this->render('quiz/start.html.twig', [
            'course' => $course,
            'chapter' => $chapter,
            'quiz' => $quiz,
            'questions' => $questions,
            'enrollment' => $enrollment
        ]);
    }

    #[Route('/course/{course_id}/chapter/{chapter_id}/quiz/{id}/submit', name: 'app_quiz_submit', methods: ['POST'])]
    public function submit(Request $request, int $course_id, int $chapter_id, int $id): JsonResponse
    {
        $quiz = $this->entityManager->getRepository(Quiz::class)->find($id);
        if (!$quiz) {
            return $this->json(['error' => 'Quiz non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $user = $this->getUser();
        if (!$user instanceof Student) {
            return $this->json(['error' => 'Non autorisé'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['error' => 'Données invalides'], Response::HTTP_BAD_REQUEST);
        }

        $questions = $quiz->getQuestions();
        $totalQuestions = count($questions);
        $correctAnswers = 0;
        $totalPoints = 0;

        foreach ($questions as $question) {
            if (isset($data[$question->getId()]) && $data[$question->getId()] === $question->getCorrectAnswer()) {
                $correctAnswers++;
                $totalPoints += $question->getPoints();
            }
        }

        $score = ($correctAnswers / $totalQuestions) * 100;
        $passed = $score >= $quiz->getMinimumScore();

        if ($passed) {
            $enrollment = $this->entityManager->getRepository(Enrollment::class)->findOneBy([
                'student' => $user,
                'course' => $quiz->getChapter()->getCourse()
            ]);

            if ($enrollment) {
                $completedChapters = $enrollment->getCompletedChapters();
                $completedChapters[] = $quiz->getChapter()->getId();
                $enrollment->setCompletedChapters(array_unique($completedChapters));
                $this->entityManager->flush();
            }
        }

        return $this->json([
            'success' => true,
            'score' => $score,
            'passed' => $passed,
            'correctAnswers' => $correctAnswers,
            'totalQuestions' => $totalQuestions
        ]);
    }
} 