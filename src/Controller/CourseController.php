<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Course;

class CourseController extends AbstractController
{
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

    #[Route('/course/{id}', name: 'app_course_show')]
    public function show(Course $course): Response
    {
        return $this->render('course/show.html.twig', [
            'course' => $course,
        ]);
    }
} 