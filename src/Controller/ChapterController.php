<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Course;
use App\Entity\Enrollment;
use App\Entity\Student;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChapterController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/course/{course_id}/chapter/{id}', name: 'app_chapter_show')]
    public function show(Course $course, Chapter $chapter): Response
    {
        /** @var Student $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si l'utilisateur est inscrit au cours
        $enrollment = $this->entityManager->getRepository(Enrollment::class)->findOneBy([
            'student' => $user,
            'course' => $course
        ]);

        if (!$enrollment) {
            $this->addFlash('error', 'Vous devez être inscrit à ce cours pour accéder à son contenu.');
            return $this->redirectToRoute('app_course_show', ['id' => $course->getId()]);
        }

        // Mettre à jour la dernière date d'accès
        $enrollment->setLastAccessAt(new DateTimeImmutable());
        $this->entityManager->flush();

        // Récupérer les chapitres précédent et suivant pour la navigation
        $chapters = $course->getChapters()->toArray();
        usort($chapters, fn($a, $b) => $a->getPosition() <=> $b->getPosition());
        
        $currentIndex = array_search($chapter, $chapters);
        $previousChapter = $currentIndex > 0 ? $chapters[$currentIndex - 1] : null;
        $nextChapter = $currentIndex < count($chapters) - 1 ? $chapters[$currentIndex + 1] : null;

        return $this->render('chapter/show.html.twig', [
            'course' => $course,
            'chapter' => $chapter,
            'enrollment' => $enrollment,
            'previousChapter' => $previousChapter,
            'nextChapter' => $nextChapter,
        ]);
    }

    #[Route('/course/{course_id}/chapter/{id}/complete', name: 'app_chapter_complete', methods: ['POST'])]
    public function complete(Course $course, Chapter $chapter, Request $request): Response
    {
        /** @var Student $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Non autorisé'], Response::HTTP_UNAUTHORIZED);
        }

        $enrollment = $this->entityManager->getRepository(Enrollment::class)->findOneBy([
            'student' => $user,
            'course' => $course
        ]);

        if (!$enrollment) {
            return $this->json(['error' => 'Inscription non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $enrollment->addCompletedChapter($chapter->getId());
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'progressPercentage' => $enrollment->getProgressPercentage()
        ]);
    }
} 