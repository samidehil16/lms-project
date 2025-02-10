<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Course;
use App\Entity\Student;
use App\Entity\Enrollment;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\ReviewService;

class CourseController extends AbstractController
{
    public function __construct(
        private ReviewService $reviewService,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/courses', name: 'app_courses')]
    public function index(Request $request, CourseRepository $courseRepository, CategorieRepository $categorieRepository): Response
    {
        $category = $request->query->get('category');
        $minPrice = $request->query->get('min_price') ? (float) $request->query->get('min_price') : null;
        $maxPrice = $request->query->get('max_price') ? (float) $request->query->get('max_price') : null;

        $courses = $courseRepository->findByFilters($category, $minPrice, $maxPrice);
        $categories = $categorieRepository->findAll();

        return $this->render('course/index.html.twig', [
            'courses' => $courses,
            'categories' => $categories,
            'currentCategory' => $category,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
        ]);
    }

    #[Route('/course/{id}', name: 'app_course_show', methods: ['GET'])]
    public function show(Course $course): Response
    {
        // Vérifie si l'utilisateur peut voir le cours
        $this->denyAccessUnlessGranted('view', $course);

        // Mettre à jour la moyenne des notes
        $this->reviewService->updateCourseRating($course);
        $ratingData = $this->reviewService->getFormattedRating($course);

        /** @var Student|null $user */
        $user = $this->getUser();
        $enrollment = null;
        
        if ($user instanceof Student) {
            $enrollment = $this->entityManager->getRepository(Enrollment::class)->findOneBy([
                'student' => $user,
                'course' => $course
            ]);
        }

        return $this->render('course/show.html.twig', [
            'course' => $course,
            'rating' => $ratingData,
            'enrollment' => $enrollment,
            'completed_chapters' => $enrollment ? $enrollment->getCompletedChapters() : [],
            'progress' => $enrollment ? $enrollment->getProgressPercentage() : 0
        ]);
    }

    #[Route('/my-courses', name: 'app_my_courses')]
    public function myCourses(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        /** @var Student $user */
        $user = $this->getUser();
        
        if (!$user instanceof Student) {
            throw $this->createAccessDeniedException('Seuls les étudiants peuvent accéder à cette page');
        }

        return $this->render('course/my_courses.html.twig', [
            'courses' => $user->getEnrolledCourses()
        ]);
    }

    #[Route('/course/{id}/edit', name: 'app_course_edit', methods: ['GET', 'POST'])]
    public function edit(Course $course): Response
    {
        $this->denyAccessUnlessGranted('edit', $course);
        
        // ... logique d'édition
        return new Response('Page d\'édition à implémenter');
    }

    #[Route('/course/{id}/delete', name: 'app_course_delete', methods: ['POST'])]
    public function delete(Course $course): Response
    {
        $this->denyAccessUnlessGranted('delete', $course);
        
        // ... logique de suppression
        return new Response('Suppression à implémenter');
    }
} 